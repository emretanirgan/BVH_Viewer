
		
		
		//variables
		var traceOn = true;
		var paused = false;
		var apps = [];
		var camera, scene = -1;
		var gcount = 0;
		var ghostArray = new Array();
		var limbsArray = new Array();
		var frameCount = 0;
		var theBody = -1;
		var k = 0;
		var noMovement = new Array();
		var renderer, root, stats;
		var movement = new Array();
		var theWorldBody = new Array();
		var ghostLegMaterial = new THREE.MeshLambertMaterial({color: 0x6666FF, opacity:0.5});
		var offset = 0;
		var tracker = 0;
	
		var jointChannels = new Array();
		var jointIndices = new Array();
		theBody = new Array();
		var legMaterial = new THREE.MeshLambertMaterial({color: 0x6666FF});

			var tester = new Array();
		
		var fps = 60;
		var ender = 0;
		var movementStart = 0;
		var loc = 0;
		var placeKeep = -1;
		var oBracket = 0;
		var cBracket = 0;
		var track = 0;
		var otrack = -1;
		var t =0;
		var BodyHolder = 0;

		//As long as you keep a consistent rotation order in the BVH file, this should work
		var rotationOrder = new Array();

		init();
	
		function toggleTrace(){
			traceOn = !traceOn;
		}
		
		function init() {

			var w = 300;
			var h = 250;

			var fullWidth = w * 2;
			var fullHeight = h * 2;

			apps.push( new App( 'container', fullWidth, fullHeight, w * 0, h * 0, w, h ) );
			

		}
			/*document.getElementById("scaleSlider").bind("slider:changed", function (event, data) {
		  console.log("here");
		  // The currently selected value of the slider
		  //alert(data.value);

		  // The value as a ratio of the slider (between 0 and 1)
		  //alert(data.ratio);
		  console.log(data.ratio);
		  scaleWorld(data.ratio, data.ratio, data.ratio);
		});*/
		/*$("#scaleSlider").bind("slider:changed", function (event, data) {
		  console.log("here");
		  // The currently selected value of the slider
		  //alert(data.value);

		  // The value as a ratio of the slider (between 0 and 1)
		  //alert(data.ratio);
		  console.log(data.ratio);
		  scaleWorld(data.ratio, data.ratio, data.ratio);
		});*/
		

		function setUpVector(x,y,z){
			//if camera is -1, it hasn't been created yet
			if(camera != -1){
				camera.up.set(x,y,z);
			}
		}

		function changeFrame(skipSize){
			//k+=skipSize;
			animateFrame();
		}

		function pauseAnim(){
			paused = !paused;
		}

		function replayAnim(){
			if(scene != -1){
				/*for(var i=scene.objects.length - 1; i >= 0; i--){
					obj = scene.objects[i];
					if(obj !== camera){
						scene.removeObject(obj);
					}
				}*/
				tracker = ((theBody.length-noMovement.length) *3) + 6;
				k = 0;
				offset = 0;
				gcount = 0;
				animate();
			}
		}
		function scaleWorld(x,y,z){

			if(scene != -1){
				theBody[0].scale = theBody[0].scale.multiplyScalar(x);
				//scene.objects[0].scale = scene.objects[0].scale.multiplyScalar(x);
				for(var i=0; i<ghostArray.length; i++){
					ghostArray[i][0].scale = ghostArray[i][0].scale.multiplyScalar(x);
				}
			}
		}

		function scaleBones(x,y,z){

			if(scene != -1){
				scaleBodyBones(theBody[0], x, y, z);
				drawLimbs(theBody, 1, false);
				

			}
		}

		function scaleBodyBones(parent, x, y, z){
			for(var i= (parent.children.length - 1); i>=0; i--){
				var child = parent.children[i];
				scaleBodyBones(parent.children[i],x,y,z);
				child.position.x = child.position.x*x;
				child.position.y = child.position.y*y;
				child.position.z = child.position.z*z;
				if (limbsArray.indexOf(child) > -1){
					parent.removeChild(child);
					scene.removeObject(child);
				}
			}
		}

		function removeLimbs(parent){
			for(var i= (parent.children.length - 1); i>=0; i--){
				var child = parent.children[i];
				removeLimbs(child);
				if (limbsArray.indexOf(child) > -1){
					parent.removeChild(child);
					scene.removeObject(child);
				}
			}
		}
		
		
		function App( containerId, fullWidth, fullHeight, viewX, viewY, viewWidth, viewHeight) {	
			
			



			//takes the PHP array and stores it into a javascript array.
			<?php 
				for($i = 0; $i < count($hmm); $i++)
				{
					echo "tester[$i]='".$hmm[$i]."';\n";
				}
			?>
			
			//The 13, 14 and 15th items in the bvh file shows what order the rotation will be in
			rotationOrder[0] = tester[13].charAt(0);
			rotationOrder[1] = tester[14].charAt(0);
			rotationOrder[2] = tester[15].charAt(0);
			console.log(rotationOrder[0] + rotationOrder[1] + rotationOrder[2]);

			parseJoint(1, null);
			frameCount = tester[movementStart+1];
			fps = 1 / (tester[movementStart+4]*1);
			//frame time?	
			movementStart += 5;
			s = tester.length;
			
			//puts the movements into a seperate array.
			for(var j = movementStart; j < tester.length; j++)
			{
					movement[k] = tester[j] *1;
					k++;
			}
			
			function findJoint (joint){
				for(var i = 0; i<theBody.length; i++){
					if(joint.id == theBody[i].id){
						return theBody[i];
					}
				}
			}
			function parseJoint(startIndex, parent){
				jointIndices.push(startIndex);
				var joint = new THREE.Mesh(new THREE.SphereGeometry(2,20,20), legMaterial);
				theBody.push(joint);
				loc++;
				var i = startIndex+1;
				for(i; i < tester.length; i++){
					if(tester[i] == '{'){
						oBracket++;
					}
					else if(tester[i] == '}'){
						cBracket++;
						if(startIndex != 1){
							return i-startIndex;
						}
					}
					else if(tester[i] == 'OFFSET'){
						joint.position.x = tester[i+1]*1;
						joint.position.y = tester[i+2]*1;
						joint.position.z = tester[i+3]*1;

						if(parent != null){
							var bodyParent = findJoint(parent);
							parent.addChild(joint);
						}
						
					}
					else if(tester[i] == 'CHANNELS'){
						
						jointChannels.push(tester[i+1]); 
					}
					else if(tester[i] == 'JOINT'){
						i += parseJoint(i, joint);
					}
					else if(tester[i] == 'End'){
						noMovement[ender] = loc - 1;
						ender++;
						i += parseJoint(i, joint);
					}
					else if(tester[i] == 'MOTION'){
						movementStart = i+1;
						return;
					}
				}
			}
			
			
			
 			initialize();
			animate();
 			//sets up the scene.
			function initialize() {
 
 				//here we are setting a container to put the webgl scene in. You initalize paramters in 
				//css at the top and then place the scene into that container here.
				
				var container = document.getElementById( containerId );
 
 				//trackball camera -> extremely useful 
				//I added small changes to the code so you must press ALT to make it work.
				camera = new THREE.TrackballCamera({ 
					
					//all the parameters you can tweak to get desired result.
					fov: 60, 
					aspect: 800 / 500,
					near: 1,
					far: 1e7,
				
				
					domElement: container,
					rotateSpeed: .8,
					zoomSpeed: 1.2,
					panSpeed: 0.8,

					noZoom: false,
					noPan: false,

					staticMoving: true,
					dynamicDampingFactor: 0.3,

		
				});
		
				//screen.width = container.clientWidth;
				//screen.height = container.clientHeight;
 				camera.position.z = 300;

				//setting and adding the skeleton we made to the scene.
				scene = new THREE.Scene();
				scene.addObject( theBody[0] );
				
				//EULER ORDER -TESTING
				for(var i=0; i<theBody.length; i++){
					theBody[i].eulerOrder = rotationOrder[0]+rotationOrder[1]+rotationOrder[2];
					//console.log(theBody[i].eulerOrder);
				}
				//make all the ghost spheres and limbs and store them in a double array.
				makeGhostArray(theBody);

				//here we initalize a renderer, rendering in WebGL allows us to use 3D
				renderer = new THREE.WebGLRenderer();
				renderer.setSize( container.clientWidth, container.clientHeight );
				container.appendChild( renderer.domElement );
				
								
				//since we are only given the positions of the spheres, this method will draw the limbs to connect them.					
				drawLimbs(theBody, 0, false);	
				var boundingbox = getBoundingBox(theBody[0]);
				console.log(boundingbox);
				var xlen = boundingbox.max.x - boundingbox.min.x;
				var ylen = boundingbox.max.y - boundingbox.min.y;
				var zlen = boundingbox.max.z - boundingbox.min.z;
				var maxlen = Math.max(xlen,ylen,zlen);
				if (maxlen == xlen){
					setUpVector(1,0,0);
				}
				else if (maxlen == ylen){
					setUpVector(0,0,1);
				}
				else if (maxlen == zlen){
					setUpVector(0,1,0);
				}
				console.log(Math.max(xlen,ylen,zlen));
				//drawAllLimbs(theBody[0]);	
					
                //Draw the bottom grid
                var geometry = new THREE.Geometry();
                geometry.vertices.push( new THREE.Vertex( new THREE.Vector3( - 500, 0, 0 ) ) );
                geometry.vertices.push( new THREE.Vertex( new THREE.Vector3( 500, 0, 0 ) ) );
                material = new THREE.LineBasicMaterial( { color: 0xffffff, opacity: 0.3 } );
        
                for ( var i = 0; i <= 10; i ++ ) 
				{
        
                    var line = new THREE.Line( geometry, material );
                    line.position.y = 0;
                    line.position.z = ( i * 100 ) - 500;
                    scene.addObject( line );
				
        
                    var line = new THREE.Line( geometry, material );
                    line.position.x = ( i * 100 ) - 500;
                    line.position.y = 0;
                    line.rotation.y = 90 * Math.PI / 180;
                    scene.addObject( line );

                    /*var line = new THREE.Line( geometry, material );
                    line.position.x = ( i * 100 ) - 500;;
                    line.position.y = 0;
                    line.rotation.z = 90 * Math.PI / 180;
                    scene.addObject( line );*/
				
				
        
                 }
				 
				 
				 
				  stats = new Stats();
 				  stats.domElement.style.position = 'absolute';
 				  stats.domElement.style.top = '0px';
				  
 				  container.appendChild( stats.domElement );
				  
								
			}
 
		
			function makeGhostArray(theArray)
			{
					var offset = 0;
					var gcount = 0;
											
				for(var s = 0; s < frameCount; s++)
					{
						
						for(var joint = 0; joint < theBody.length; joint++)
						{
							for(var theEnd = 0; theEnd < noMovement.length; theEnd++)
							{
								//if it is an end joint it does not rotate, so we skip it.
								if(joint == noMovement[theEnd])
								{
									joint++;

								}
							}

							//Prevent index out of bounds exception in case the last joint was an end joint
							if (joint >= theArray.length){
								break;
							}

							//if it is the root it has rotations and positions. we handle this seperately.
							if(joint == 0)
							{
								theArray[joint].position.x = movement[offset++];
								theArray[joint].position.z = -movement[offset++];
								theArray[joint].position.y = movement[offset++];

								for(var i = 0; i < 3; i++){
									if(rotationOrder[i] == "X"){
										theArray[joint].rotation.x = Math.PI/180 * movement[offset] + Math.PI /180 * -90;
									}
									else if(rotationOrder[i] == "Y"){
										theArray[joint].rotation.y = Math.PI/180 * movement[offset] ;
									}
									else if(rotationOrder[i] == "Z"){
										theArray[joint].rotation.z = Math.PI/180 * movement[offset];
									}
									offset++;
								}
							
				
							}
				
							//if it's not the root it has three rotations like everything else.
							else
							{	
								for(var i = 0; i < 3; i++){
									if(rotationOrder[i] == "X"){
										theArray[joint].rotation.x = movement[offset] * Math.PI/180;
									}
									else if(rotationOrder[i] == "Y"){
										theArray[joint].rotation.y = movement[offset] * Math.PI/180;
									}
									else if(rotationOrder[i] == "Z"){
										theArray[joint].rotation.z = movement[offset] * Math.PI/180;
									}
									offset++;
								}
								
							}
							
						}
							if(s % 100 ==0 && s > 99)
							{
							
								
									ghostArray[gcount] = new Array(30);
									for(var i = 0; i < theBody.length; i++)
									{
										
										root = new THREE.Mesh( new THREE.SphereGeometry(2,20,20), ghostLegMaterial);
										root.position.x = theArray[i].position.x;
										root.position.y = theArray[i].position.y;
										root.position.z = theArray[i].position.z;
								


										root.rotation.x = theArray[i].rotation.x;
										root.rotation.y = theArray[i].rotation.y;
										root.rotation.z = theArray[i].rotation.z;
									
										ghostArray[gcount][i] = root;
							
									}
									for(var r = 0; r < theBody.length; r++)
									{
										for(var t = 0; t < theBody.length; t++)
											{
												if(theBody[r].parent == theBody[t])
												{	
													ghostArray[gcount][t].addChild(ghostArray[gcount][r]);	
												}
											}
									}
						
			
								drawLimbs(ghostArray,gcount,true);
								gcount++;
							
			
					
					}
	
				}
			
			}
		

			//function to draw the limbs.
			//num is only used for the ghost array.
			//double determines whether this is a doubleArray
			
			//this is so we know how many numbers there will be for each frame.
			tracker = ((theBody.length-noMovement.length) *3) + 6;
			gcount = 0;
			k = 0;	 
			offset = 0;
			
		}