import os

basedir = "/Users/Joseph/Desktop/cis462oct2012"
mocapRoot = "https://fling.seas.upenn.edu/~mocap"

if __name__ == '__main__':
#    wsdir = "ADirectoryToTestAndCreate"
#    if not os.path.exists(wsdir):
#       os.mkdir(wsdir)
#    print "Setting workspace", wsdir

    file = open("mocapLinks.txt", 'w')

    for (dirpath,dirnam,filenames) in os.walk(basedir):
	pathSplit = dirpath.split("/Users/Joseph/Desktop")
	relativePath = pathSplit[len(pathSplit)-1]
	temp = relativePath.split(" ")
	relativePath = "%20".join(temp)

        for f in filenames:
	    if f[-4:len(f)] == ".vsk": 
		openfile = f.split(" ")
		file.write(mocapRoot+relativePath+"/"+"%20".join(openfile)+'\n')
        for f in filenames:
            if f[-4:len(f)] == ".c3d":  # Test for a file extension
                openfile = f.split(" ")
                file.write(mocapRoot+relativePath+"/"+"%20".join(openfile)+'\n')

