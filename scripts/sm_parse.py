import sys
import json
from simfile import *
from pprint import pprint

filename = sys.argv[1]

simfile = {}
simfile['song'] = {}

try:
	sim = Simfile(filename)
except:
	print "ERROR"
	#print "ERROR"
	exit()
try:
	simfile['song']['title'] = sim['TITLE']
except:
	simfile['song']['title'] = ""

try:
	simfile['song']['artist'] = sim['ARTIST']
except:
	simfile['song']['artist'] = ""

try:
	simfile['song']['banner'] = sim['BANNER']
except:
	simfile['song']['banner'] = ""

try:
	simfile['song']['background'] = sim['BACKGROUND']
except:
	simfile['song']['background'] = ""

try:
	simfile['song']['credit'] = sim['CREDIT']
except:
	simfile['song']['credit'] = ""

try:
	simfile['song']['subtitle'] = sim['SUBTITLE']
except:
	simfile['song']['subtitle'] = ""

try:
	simfile['song']['artisttranslit'] = sim['ARTISTTRANSLIT']
except:
	simfile['song']['artisttranslit'] = ""

try:
	simfile['song']['titletranslit'] = sim['TITLETRANSLIT']
except:
	simfile['song']['titletranslit'] = ""

try:
	simfile['song']['subtitletranslit'] = sim['SUBTITLETRANSLIT']
except:
	simfile['song']['subtitletranslit'] = ""

try:
	simfile['song']['bgchanges'] = sim['BGCHANGES']
except:
	simfile['song']['bgchanges'] = ""

try:
	simfile['song']['fgchanges'] = sim['FGCHANGES']
except:
	simfile['song']['fgchanges'] = ""

simfile['song']['charts'] = {}

for chart in sim.charts:
	type = str(chart.stepstype)
	if not simfile['song']['charts'].get(type):
        	simfile['song']['charts'][type] = {}
	simfile['song']['charts'][type][chart.difficulty] = {}
        simfile['song']['charts'][type][chart.difficulty]['meter'] = chart.meter
        simfile['song']['charts'][type][chart.difficulty]['description'] = chart.description

	if chart.stepstype in 'dance-single':
		simfile['song']['charts'][type][chart.difficulty]['notes'] = {}
		taps = 0
		holds = 0
		jumps = 0
		mines = 0
		rolls = 0
		for notes in chart.notes:
			if notes[1] in ("1001","0110","1100","0011", "1010", "0101"):
				jumps+=1
				taps+=2			
			elif notes[1] in ("2002","0220","2200","0022", "2020", "0202"):
				jumps+=1
				holds+=2
				taps+=2
			elif notes[1] in ("1002","0120","1200","0012", "1020", "0102", "2001","0210","2100","0021", "2010", "0201"):
				jumps+=1
				holds+=1
				taps+=2
			else:
				for l in list(notes[1]):
					if l in ("M", "m"):
						mines+=1
					elif int(l) == 1:
						taps+=1
					elif int(l) == 2:
        	                                holds+=1
						taps+=1
					elif int(l) == 4:
						rolls+=1
						taps+=1
					#else:
		simfile['song']['charts'][type][chart.difficulty]['notes']['taps'] = taps
                simfile['song']['charts'][type][chart.difficulty]['notes']['holds'] = holds
                simfile['song']['charts'][type][chart.difficulty]['notes']['jumps'] = jumps
                simfile['song']['charts'][type][chart.difficulty]['notes']['mines'] = mines
                simfile['song']['charts'][type][chart.difficulty]['notes']['rolls'] = rolls

print json.dumps(simfile, sort_keys=True, indent=4)
