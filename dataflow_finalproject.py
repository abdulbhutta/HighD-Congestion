import argparse
import json
import logging
import os
import apache_beam as beam
import tensorflow as tf
from apache_beam.options.pipeline_options import PipelineOptions
from apache_beam.options.pipeline_options import SetupOptions
from beam_nuggets.io import relational_db
import time

#Create a batch pipeline to read from the pubsub topic and write to the database
def run(argv=None, save_main_session=True):
  parser = argparse.ArgumentParser()
  known_args, pipeline_args = parser.parse_known_args(argv)

  pipeline_options = PipelineOptions(pipeline_args)
  pipeline_options.view_as(SetupOptions).save_main_session = save_main_session

  #read csv file from the bucket and extract the class column using batch pipeline  
  with beam.Pipeline(options=pipeline_options) as p:
    output_config = relational_db.SourceConfiguration(
    drivername='mysql+pymysql',
    host='34.118.155.224',
    port=3306,
    username = 'usr',
    password = 'sofe4630u',
    database = 'Readings'
    )

    table_config = relational_db.TableConfiguration(
        name='processedData',
        create_if_missing=True,
        primary_key_columns=['time']
    )

    table_config2 = relational_db.TableConfiguration(
        name='highD',
        create_if_missing=True,
        primary_key_columns=['time']
    )

    def sumDict(values):
        sumDicts={'time': 0, 'no_lane_changes': 0, 'one_lane_change': 0, 'two_lane_change': 0}
        for v in values:
            for key in v.keys():
                sumDicts[key]+=v[key]
        return sumDicts

    def sumDict2(values):
        sumDicts={'time': 0, 'car_direction1': 0, 'car_direction2': 0, 'truck_direction1': 0, 'truck_direction2': 0}
        for v in values:
            for key in v.keys():
                sumDicts[key]+=v[key]
        return sumDicts

#id	width	height	initialFrame	finalFrame	numFrames	class	drivingDirection	traveledDistance	minXVelocity	maxXVelocity	meanXVelocity	minDHW	minTHW	minTTC	numLaneChanges

    data = (p | 'Read from csv' >> beam.io.ReadFromText('gs://sofe4630u-finalproject-bucket/01_tracksMeta.csv', skip_header_lines=1)
    | 'Convert to dictionary' >> beam.Map(lambda x: {"time": time.time(),'numFrames': x.split(',')[5], 'class': x.split(',')[6], 'drivingDirection': x.split(',')[7], 'meanXVelocity': x.split(',')[11], 'numLaneChanges': x.split(',')[15]})
    )

    #Write to text file in the bucket
    #data | 'Write to text' >> beam.io.WriteToText('gs://sofe4630u-finalproject-bucket/Output.txt')
    data | "Write Preprocessed Data to SQL" >> relational_db.Write(source_config=output_config, table_config=table_config2)


    #Filter the data to be less than 50 frames and write to text file in the bucket
    filteredData = (data | 'Filter number of frames' >> beam.Filter(lambda x: int(x['numFrames']) > 50)
    )

    #From filtered data dictionary, extract the class column and driving direction column and extract drivingDirection to calculate the number of cars and trucks in each direction
    classData = (filteredData | 'Extract class' >> beam.Map(lambda x: (x['class'], x['drivingDirection']))
    )

    countDirData = (classData | 'Counting Vechicles in both Directions' >> beam.combiners.Count.PerElement()

    #set keys 
    | 'Set the key/value pair for each vehicle and directions' >> beam.Map(lambda x: {
            'time': 0,
            'car_direction1': x[1] if x[0][0] == 'Car' and x[0][1]=='1' else 0,
            'car_direction2': x[1] if x[0][0] == 'Car' and x[0][1]=='2' else 0,
            'truck_direction1': x[1] if x[0][0] == 'Truck' and x[0][1]=='1' else 0,
            'truck_direction2': x[1] if x[0][0] == 'Truck' and x[0][1]=='2' else 0
    }) | 'Sum up values for Car Direction' >> beam.CombineGlobally(sumDict2)

    #Write the result to a text file in the bucket
    | "Write the Total Car and Truck direction in each lane" >> relational_db.Write(source_config=output_config, table_config=table_config))
    #Write the countDirData to a text file in the bucket
    #countDirData | 'Write countdir to text' >> beam.io.WriteToText('gs://sofe4630u-finalproject-bucket/countdir.txt')

    #Get the meanxvelocity column from the filtered data and convert the values to float
    meanXVelocity = (filteredData | 'Get meanXVelocity' >> beam.Map(lambda x: float(x['meanXVelocity']))
        #Get the mean value of the meanXVelocity column
      | 'Get mean value' >> beam.combiners.Mean.Globally()

      | 'Set the key/value pair for mean value' >> beam.Map(lambda x: {'time':0,'mean_value': x})
      #write the mean value to the database
      | "Write the average speed of the highway" >> relational_db.Write(source_config=output_config, table_config=table_config)       
    )

    #Get the numLaneChanges column and convert to int
    numLaneChanges = (filteredData | 'Get number of lane changes' >> beam.Map(lambda x: int(x['numLaneChanges']))
    
    #Calculate the number of non zero values in the numLaneChanges column and for each element in the PCollection put the value in a dictionary with the key being the number of non zero values
    | 'Count non zero values' >> beam.combiners.Count.PerElement()

    #set keys 
    | 'Set the key/value pair value for each lane' >> beam.Map(lambda x: {
            'time': 0,
            'no_lane_changes': x[1] if x[0] == 0 else 0,
            'one_lane_change': x[1] if x[0] == 1 else 0,
            'two_lane_change': x[1] if x[0] == 2 else 0,
    }))

    #sum the values for each key 
    numLaneChanges= (numLaneChanges | 'Sum up values for Lane Change' >> beam.CombineGlobally(sumDict)#.without_defaults()

    #write to text file in the bucket
    #| 'Write to text' >> beam.io.WriteToText('gs://sofe4630u-finalproject-bucket/laneChanges.txt')

    #Write the result to a text file in the bucket
    | "Write the results for each lane to SQL" >> relational_db.Write(source_config=output_config, table_config=table_config)   
    )

if __name__ == '__main__':
  logging.getLogger().setLevel(logging.INFO)
  run()