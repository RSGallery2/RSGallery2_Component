#!/usr/bin/python

"""
Collection of joomla TranslationFile from one file
"""


import os
#import re
import getopt
import sys

from datetime import datetime


HELP_MSG = """
TranslationFile supports ...

usage: TranslationFile.py -? nnn -? xxxx -? yyyy  [-h]
	-? nnn
	-?


	-h shows this message

	-1
	-2
	-3
	-4
	-5


	example:


------------------------------------
ToDo:
ToDo:
  *
  *
  *
  *
  *

"""

#-------------------------------------------------------------------------------
LeaveOut_01 = False
LeaveOut_02 = False
LeaveOut_03 = False
LeaveOut_04 = False
LeaveOut_05 = False

#-------------------------------------------------------------------------------

# ================================================================================
# TranslationFile
# ================================================================================

class TranslationFile:

	""

	#---------------------------------------------
	def __init__ (self, translationFile=''):
		print( "Init TranslationFile: ")
		print ("translationFile: " + translationFile)
		self.translationFile = translationFile
#		self.LocalPath = LocalPath
		self.translations = {}
		self.doubles = {}

		if (os.path.isfile(translationFile)):
			self.load ()


	def load (self, fileName=''):
		try:
			print ('*********************************************************')
			print ('load')
			print ('fileName: ' + fileName)

			print ('---------------------------------------------------------')

			self.translations = {}
			self.doubles = {}

			#---------------------------------------------
			# Read file
			#---------------------------------------------

			if fileName == '' :
				fileName = self.translationFile

				if (os.path.isfile(fileName)):
					print ('Found fileName: ' + fileName)
					#print ('fileName: ' + fileName)

					with open(fileName, encoding="utf8") as fp:
						for cnt, line in enumerate(fp):
							#if LookupString not in line:
							#	continue
							line = line.strip()

							idx = line.find ('=')

							#if '=' not in line:
							if (idx < 0):
								continue
							
							# comment
							if (line[0] == ';'):
								continue

							transId = line[:idx].strip ()

							transText = line[idx+1:].strip ()
							#print ('transText (1): ' + transText)
							# Remove ""
							transText = transText [1:-1]
							#print ('transText (2): ' + transText)
							
							# prepared lines in file : com... = ""
							if (len(transText) < 1):
								continue


							# Key does already exist
							if (transId in self.translations):
								# Save last info
								self.doubles [transId] = self.translations [transId]

							self.translations [transId] = transText



			return


			#--------------------------------------------------------------------
			#
			#--------------------------------------------------------------------



			#--------------------------------------------------------------------
			#
			#--------------------------------------------------------------------


			#--------------------------------------------------------------------
			#
			#--------------------------------------------------------------------




		finally:
			print ('exit TranslationFile')

	#-------------------------------------------------------------------------------
	# ToDo: Return string instead of print
	def Text (self):
		#print ('    >>> Enter yyy: ')
		#print ('       XXX: "' + XXX + '"')

		ZZZ = ""

		try:
			print ("Translations: " + str(len (self.translations)))
			for key, value in self.translations.items():
				print ("   " + key + " = " + value)

			print ("Doubles: " + str(len (self.doubles)))
			for key, value in self.doubles.items():
				print ("   " + key + " = " + value)

		except Exception as ex:
			print(ex)

#	print ('    <<< Exit yyy: ' + ZZZ)
#	return ZZZ

toDo: further checks ?


##-------------------------------------------------------------------------------
##
#def yyy (XXX):
#	print ('    >>> Enter yyy: ')
#	print ('       XXX: "' + XXX + '"')
#
#	ZZZ = ""
#
#	try:
#
#
#	except Exception as ex:
#		print(ex)
#
#	print ('    <<< Exit yyy: ' + ZZZ)
#	return ZZZ


##-------------------------------------------------------------------------------
##
#def yyy (XXX):
#	print ('    >>> Enter yyy: ')
#	print ('       XXX: "' + XXX + '"')
#
#	ZZZ = ""
#
#	try:
#
#
#	except Exception as ex:
#		print(ex)
#
#	print ('    <<< Exit yyy: ' + ZZZ)
#	return ZZZ

##-------------------------------------------------------------------------------
##
#def yyy (XXX):
#	print ('    >>> Enter yyy: ')
#	print ('       XXX: "' + XXX + '"')
#
#	ZZZ = ""
#
#	try:
#
#
#	except Exception as ex:
#		print(ex)
#
#	print ('    <<< Exit yyy: ' + ZZZ)
#	return ZZZ


##-------------------------------------------------------------------------------

def dummyFunction():
	print ('    >>> Enter dummyFunction: ')
	#print ('       XXX: "' + XXX + '"')


##-------------------------------------------------------------------------------

def Wait4Key():
	try:
		input("Press enter to continue")
	except SyntaxError:
		pass


def testFile(file):
	exists = os.path.isfile(file)
	if not exists:
		print ("Error: File does not exist: " + file)
	return exists

def testDir(directory):
	exists = os.path.isdir(directory)
	if not exists:
		print ("Error: Directory does not exist: " + directory)
	return exists

def print_header(start):

	print ('------------------------------------------')
	print ('Command line:', end='')
	for s in sys.argv:
		print (s, end='')

	print ('')
	print ('Start time:   ' + start.ctime())
	print ('------------------------------------------')

def print_end(start):
	now = datetime.today()
	print ('')
	print ('End time:               ' + now.ctime())
	difference = now-start
	print ('Time of run:            ', difference)
	#print ('Time of run in seconds: ', difference.total_seconds())

# ================================================================================
#   main (used from command line)
# ================================================================================

if __name__ == '__main__':
	optlist, args = getopt.getopt(sys.argv[1:], 'l:r:12345h')

	langFile = '..\\..\\admin\language\en-GB\en-GB.com_rsgallery2.ini'


	for i, j in optlist:
		if i == "-l":
			LeftPath = j
		if i == "-r":
			RightPath = j

		if i == "-h":
			print (HELP_MSG)
			sys.exit(0)

		if i == "-1":
			LeaveOut_01 = True
			print ("LeaveOut_01")
		if i == "-2":
			LeaveOut_02 = True
			print ("LeaveOut__02")
		if i == "-3":
			LeaveOut_03 = True
			print ("LeaveOut__03")
		if i == "-4":
			LeaveOut_04 = True
			print ("LeaveOut__04")
		if i == "-5":
			LeaveOut_05 = True
			print ("LeaveOut__05")


	#print_header(start)

	TransFile = TranslationFile (langFile)

	TransFile.Text ()
	#print_end(start)

