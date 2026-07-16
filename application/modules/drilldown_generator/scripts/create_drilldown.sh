#!/bin/sh

# remove the old drilldown output files
echo "removing old drilldown output files"
cd ../resources/drilldown_output && rm -rf city rm -rf organization && rm -rf person && rm -rf state

# create the drillbit files
cd ../../../../..
php index.php drilldown_creator_controller create_drillbits

# create the dump files for the drilldown
php index.php drilldown_creator_controller dump

# start creating the drilldown output files
cd application/modules/drilldown_generator/java/bin

# generate US drilldown files
echo "drilling US schools"
java -cp ./ drilldown.Miner ../../resources/drillbits/drillbit_alphabet.txt ../../resources/drilldown_dumps/organization/us ../../resources/drilldown_output/organization/us 40
echo "drilling US teachers"
java -cp ./ drilldown.Miner ../../resources/drillbits/drillbit_alphabet.txt ../../resources/drilldown_dumps/person/us ../../resources/drilldown_output/person/us 40
echo "drilling US states"
java -cp ./ drilldown.Miner ../../resources/drillbits/us/state.txt ../../resources/drilldown_dumps/state/us ../../resources/drilldown_output/state/us 40
echo "drilling US cities"
java -cp ./ drilldown.Miner ../../resources/drillbits/drillbit_alphabet.txt ../../resources/drilldown_dumps/city/us ../../resources/drilldown_output/city/us 40

# cleanup the drilldown dump files
echo "cleaning up drilldown dumps"
cd ../../resources/drilldown_dumps && rm -rf *

# cleanup the drillbit files
echo "cleaning up drillbit files"
cd ../drillbits && rm -rf us