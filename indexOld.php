#!/usr/local/bin/php
<!DOCTYPE HTML> 
<html lang="en"> 
	<head> 
		<title>WebGL BVH Player</title> 
		<meta charset="utf-8"> 
		<style type="text/css"> 
			body {
				background-color: #000000;
				margin: 0px;
				overflow: hidden;				
			}
			#container{
				background-color: #000000;
				margin: 0px;
				width:800px;
				height: 600px; 
				float: left;
				
			}
			#container2{
				background-color: #000000;
				margin: 0px;
				width:800px;
				height: 600px; 
				float:right; 
				
				
			}

		</style> 
	</head> 
	<body> 
    

    <?php 
	ini_set('memory_limit', '400M');
	$dafile = "HopScotch.bvh";
	$variable1 = file_get_contents($dafile); 

  	$hmm =  preg_split("/[_\s]+/", $variable1);
	
	
	$dafile2 = "Run.bvh";
	$variable2 = file_get_contents($dafile2); 

  	$hmm2 =  preg_split("/[_\s]+/", $variable2);
	
	
	
	?> 

 
		<div id="container"></div> 
        <div id="container2"></div> 
	
		<script type="text/javascript" src="Three.js"></script> 
		<script type="text/javascript" src="RequestAnimationFrame.js"></script> 
		<script type ="text/javascript" src="TrackballCamera.js"></script>
        <script type ="text/javascript" src="Stats.js"></script>
 	
		<script type="text/javascript"> 
 
 			//variables
		
			
			
		
			
			var apps = [];
			//calls init and animate functions.
			init();
			animate();
		
			
			
			
			function init() {
 
				var w = 300;
				var h = 250;
 
				var fullWidth = w * 2;
				var fullHeight = h * 2;
 
				apps.push( new App( 'container', fullWidth, fullHeight, w * 0, h * 0, w, h, 1 ) );
				apps.push( new App( 'container2', fullWidth, fullHeight, w * 1, h * 0, w, h, 2 ) );
				
 
			}
 
			function animate() {
 
				for ( var i = 0; i < apps.length; ++i ) {
 
					apps[ i ].animate();
 
				}
 
				requestAnimationFrame( animate );
 
			}
			
			
		function App( containerId, fullWidth, fullHeight, viewX, viewY, viewWidth, viewHeight, ID1 ) {	
			
			var camera, scene, renderer, root, stats;
			var theWorldBody = new Array();
			var ghostLegMaterial = new THREE.MeshLambertMaterial({color: 0x6666FF, opacity:0.5});
			var ghostArray = new Array();
			var offset = 0;
			var gcount = 0;
		
			
			
			
			var theBody = new Array();
			var legMaterial = new THREE.MeshLambertMaterial({color: 0x6666FF});
			var movement = new Array();
 			var tester = new Array();
			var noMovement = new Array();
			var ender = 0;
			var movementStart = 0;
			var loc = 0;
			var placeKeep = -1;
			var oBracket = 0;
			var cBracket = 0;
			var track = 0;
			var otrack = -1;
			var frameCount = 0;
			var t,k =0;
			var BodyHolder = 0;
		
		
			if(ID1 == 1)
			{
			//takes the PHP array and stores it into a javascript array.
				//tester = <?php echo json_encode($hmm); ?>;


				
				<?php 
					for($i = 0; $i < count($hmm); $i++)
					{
						echo "tester[$i]='".$hmm[$i]."';\n";
					}
				?>
				
			}
			if(ID1 == 2)
			{
				//tester = <?php echo json_encode($hmm2); ?>;
				
				<?php 
					for($i = 0; $i < count($hmm2); $i++)
					{
						echo "tester[$i]='".$hmm2[$i]."';\n";
					}
				?>
				
			}
		
			
		
			//this builds the body	
			for(var s = 0; s < tester.length; s++)
			{
				//I decided to set it up to find the correct offsets by seeing what is NOT a number
				if(isNaN(tester[s]))
				{
					//keep track of brackets for parenting purposes.
					if(tester[s] == '{')
					{
						oBracket++;	
						otrack++;
						
					}
					//if the word found is offset, we are given positions of the sphere
					else if(tester[s] == 'OFFSET')
					{
						//placetracker
						s++;

						//make a new object of size 2. and set the positions.
						root = new THREE.Mesh( new THREE.SphereGeometry(2,20,20), legMaterial);
						
						root.position.x = tester[s++] *1;
						root.position.y = tester[s++] *1;
						root.position.z = tester[s++] *1;

						//as long as this is not the original root position (0,0,0)
						if(loc!=0)
						{
								//if placeKeep is -1 the parent of this sphere is the previous position in the array
								if(placeKeep == -1)
								{
									placeKeep = loc-1;
									theBody[placeKeep].addChild(root);
								}								
								
								else
								{
									//if it is not, it is because it is an 'end' point. 
									//the placeKeep is set later down.
									theBody[placeKeep].parent.addChild(root);
								}
								
							
							
						}
						
						//default placeKeep to -1 and add the object to the array.
						placeKeep = -1;
						theBody.push(root);
						loc++;
					
						
					}
					
					//find out where the to be added in the tree if it is an end position.
					else if(tester[s] == 'End')
					{
						//make sure that we get only numbers.
						while(isNaN(tester[s]))
						{
							s++;
						}
						
						//keep track of brackets
						oBracket++;
						otrack++;
						
						//make new sphere object and sets position.
						root = new THREE.Mesh( new THREE.SphereGeometry(2,20,20), legMaterial);
						root.position.x = tester[s++] *1;
						root.position.y = tester[s++] *1;
						root.position.z = tester[s++] *1;
						
						//if the placeKeep is -1, then the previous sphere added is the parent.
						if(placeKeep == -1)
						{
							placeKeep = loc-1;
							theBody[placeKeep].addChild(root);
						}
						else{
							theBody[placeKeep].parent.addChild(root);
						}
						
						//add to the array.
						theBody.push(root);		
						loc++;
		
						//next we are finding out where the parent of the next sphere will be placed.
						if(tester[s] == 'CHANNELS')
						{
							s+=5;
						}
						else{
							//if it is an end position, there is no rotation, so i keep track of this position in the array.
							noMovement[ender] = loc-1;
							ender++;
						}
						//keep track of closing brackets
						while( tester[s] == '}')
						{
							cBracket++;
							track++;
							s++;
	
						}
					
						//here is where the parents location is computed
						 if(oBracket > cBracket)
						 {
							 //if the closing brackets are equal to the opening brackets in each branch.
							 //then the parent is the total number of open brackets minus the current closing brackets
							 if(otrack == track)
							 {
								placeKeep = oBracket - track;
								track = 0;
								otrack = 0;

							 }
							 else
							 {
 								//if not then we have a few choices.
								//we are keeping tracking of the placekeep. 
								//this is for special cases such as where the right shoulder would go. 
								// when we also have to parent the left wrist end to something.
								 placeKeep = loc - track;
								 
									 if(BodyHolder == 0)
									 {
										BodyHolder = placeKeep;
										track = 0;
										otrack =0;
									
									 }
									 else if(theBody[BodyHolder].parent != theBody[placeKeep].parent)
									 {
										 placeKeep = placeKeep;
									 }	 
								}
								 
							 }
							
						 }
						
						//this keeps track of where the actual movements will start in the array.
						else if(tester[s] == 'Frames:')
							{
								frameCount = tester[s+1];	
								movementStart= s+5;
								s = tester.length;
							}
							
					
					}
				
			}
			//puts the movements into a seperate array.
			for(var j = movementStart; j < tester.length; j++)
			{
					movement[k] = tester[j] *1;
					k++;
			}
			
			
			
			
			
			
 			init();
			animate();
 			//sets up the scene.
			function init() {
 
 				//here we are setting a container to put the webgl scene in. You initalize paramters in 
				//css at the top and then place the scene into that container here.
				
				/*var container;
				container = document.getElementById( 'container1' );
				
				var cont; 
				cont = document.getElementById('container2');*/
			
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
				
				
				//make all the ghost spheres and limbs and store them in a double array.
				makeGhostArray(theBody);

				//here we initalize a renderer, rendering in WebGL allows us to use 3D
				renderer = new THREE.WebGLRenderer();
				renderer.setSize( container.clientWidth, container.clientHeight );
				container.appendChild( renderer.domElement );
				
								
				//since we are only given the positions of the spheres, this method will draw the limbs to connect them.					
				drawLimbs(theBody);	
					
                //Draw the bottom grid
                var geometry = new THREE.Geometry();
                geometry.vertices.push( new THREE.Vertex( new THREE.Vector3( - 500, 0, 0 ) ) );
                geometry.vertices.push( new THREE.Vertex( new THREE.Vector3( 500, 0, 0 ) ) );
                material = new THREE.LineBasicMaterial( { color: 0xffffff, opacity: 0.6 } );
        
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
				
        
                 }
				 
				 
				 
				  stats = new Stats();
				  stats2 = new Stats();
 				  stats.domElement.style.position = 'absolute';
 				  stats.domElement.style.top = '0px';
				  stats2.domElement.style.position = 'absolute';
 				  stats2.domElement.style.top = '0px';
				  
				  
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
						
							//if it is the root it has rotations and positions. we handle this seperately.
							if(joint == 0)
							{
							
								theArray[joint].position.x = movement[offset++];
								theArray[joint].position.z = -movement[offset++];
								theArray[joint].position.y = movement[offset++];
								
								theArray[joint].rotation.x = Math.PI/180 * movement[offset++] + Math.PI /180 * -90;
								theArray[joint].rotation.y = Math.PI/180 * movement[offset++] ;
								theArray[joint].rotation.z = Math.PI/180 * movement[offset++];
							}
				
							//if it's not the root it has three rotations like everything else.
							else
							{	
								theArray[joint].rotation.x = movement[offset++] * Math.PI/180;
								theArray[joint].rotation.y = movement[offset++] * Math.PI/180;
								theArray[joint].rotation.z = movement[offset++] * Math.PI/180;
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
						
			
								drawDoubleLimbs(ghostArray,gcount);
								gcount++;
							
			
					
					}
	
				}
			
			}
		
		
		
		
		
		
		
	
			//function to draw the limbs.
			function drawLimbs(theArray)
			{
				var joint1, joint2, tempX, tempY, tempZ, theLimb, rotX, rotY, rotZ, distance;
				var sphereMaterial = new THREE.MeshLambertMaterial({color: 0xCC0000});
				var placementX, placementY,placementZ;
				var placementArray= new Array();
					
					//clones the array so its passed by value, not by reference
					for(var world = 0; world < theBody.length; world++)
					{
						theWorldBody[world] = (new THREE.Vertex((theBody[world].position.clone())));
						placementArray[world] = (new THREE.Vertex((theBody[world].position.clone())));
					}

					//this loop keeps track of the world positions.
					for(var world2 = 1; world2 < theWorldBody.length; world2++)
					{
						//if the parent is the previous location, just take the current local position of 
						//the sphere and add it to the previous's world position.
						if(theBody[world2].parent == theBody[world2-1])
							{
								theWorldBody[world2].position.x += (theWorldBody[world2-1].position.x);
								theWorldBody[world2].position.y += (theWorldBody[world2-1].position.y);
								theWorldBody[world2].position.z += (theWorldBody[world2-1].position.z);
							}
							else 
							{
								//if not we have to find the parent. and add the current local position to it's 
								//parent's world position
								for(var r = 0; r < theWorldBody.length-1; r++)
								{
									if(theBody[world2].parent == theBody[r])
									{
										theWorldBody[world2].position.x += (theWorldBody[r].position.x);
										theWorldBody[world2].position.y += (theWorldBody[r].position.y);
										theWorldBody[world2].position.z += (theWorldBody[r].position.z);
										//if we find it then we can exit the loop.
										r = theWorldBody.length;
										
									}
								}
								
								
							}
							
					}
				
				//here is where the calculations are done to find the correct position, length, and rotation of the cylinder limb.
				var placer, other= 0;
				for(var i = 1; i < theBody.length; i++)
				{
					//if the parent is the previous object in array, use these positons
				   	if(theBody[i].parent == theBody[i-1])
				   	{
						placer = i-1;
						other = i;
				   	}
				  	
					else
					{	
						//if not we have to find teh parent's position.
						for(var r = 0; r < theWorldBody.length; r++)
								{
									if(theBody[i].parent == theBody[r])
									{

										placer = r;
										other = i;
										//exit the loop
										r = theWorldBody.length;
										
								     }
							    }
	
						}
				   			
				   			//keep track of lengths.		
							var v1 = new THREE.Vector3(theWorldBody[placer].position.x, theWorldBody[placer].position.y, theWorldBody[placer].position.z);
							var v2 = new THREE.Vector3(theWorldBody[other].position.x, theWorldBody[other].position.y, theWorldBody[other].position.z);
							var v3 = new THREE.Vector3(v1.x-v2.x, v1.y - v2.y, v1.z - v2.z); //to find the length
							var v4 = new THREE.Vector3(0, 0, 1); //the axis vector
								
							placementX = placementArray[other].position.x;
							placementY = placementArray[other].position.y;
							placementZ = placementArray[other].position.z;	
							
							//new cylinder object
				   			theLimb = new THREE.Mesh( new THREE.CylinderGeometry( 30, 1.5, 1.5, v3.length() -2 , 1, 1), sphereMaterial );
							
							//keeps track of where to place the limb.
							placementX /= 2;
							placementY /= 2;
							placementZ /= 2;	
						
							//calculations with quaternions for rotation
							v4.normalize();
							v3.normalize();
							var crossVecs = new THREE.Vector3();
							crossVecs.cross(v4,v3);
							crossVecs.normalize();

							var dotVecs = Math.acos(v3.dot(v4)/(v3.length()*v4.length()));

							q1 = new THREE.Quaternion();
							q1.setFromAxisAngle(crossVecs, dotVecs);
							q1.normalize();
	
							if(distance !=0)
							{
						
								theLimb.useQuaternion = true;
								theLimb.quaternion = q1;

							}
							//sets the position and adds the object to the parent
							theLimb.position.x = placementX;
							theLimb.position.y = placementY;
							theLimb.position.z = placementZ;
							
						
							theArray[placer].addChild(theLimb);
					

				}
			
					
			}
			
			
			
			
			
			
						//function to draw the limbs.
			function drawDoubleLimbs(theArray, num)
			{
				var joint1, joint2, tempX, tempY, tempZ, theLimb, rotX, rotY, rotZ, distance;
				var sphereMaterial = new THREE.MeshLambertMaterial({color: 0xCC0000, opacity:0.5});
				var placementX, placementY,placementZ;
				var placementArray= new Array();
					
					//clones the array so its passed by value, not by reference
					for(var world = 0; world < theBody.length; world++)
					{
						theWorldBody[world] = (new THREE.Vertex((theBody[world].position.clone())));
						placementArray[world] = (new THREE.Vertex((theBody[world].position.clone())));
					}

					//this loop keeps track of the world positions.
					for(var world2 = 1; world2 < theWorldBody.length; world2++)
					{
						//if the parent is the previous location, just take the current local position of 
						//the sphere and add it to the previous's world position.
						if(theBody[world2].parent == theBody[world2-1])
							{
								theWorldBody[world2].position.x += (theWorldBody[world2-1].position.x);
								theWorldBody[world2].position.y += (theWorldBody[world2-1].position.y);
								theWorldBody[world2].position.z += (theWorldBody[world2-1].position.z);
							}
							else 
							{
								//if not we have to find the parent. and add the current local position to it's 
								//parent's world position
								for(var r = 0; r < theWorldBody.length-1; r++)
								{
									if(theBody[world2].parent == theBody[r])
									{
										theWorldBody[world2].position.x += (theWorldBody[r].position.x);
										theWorldBody[world2].position.y += (theWorldBody[r].position.y);
										theWorldBody[world2].position.z += (theWorldBody[r].position.z);
										//if we find it then we can exit the loop.
										r = theWorldBody.length;
										
									}
								}
								
								
							}
							
					}
				
				//here is where the calculations are done to find the correct position, length, and rotation of the cylinder limb.
				var placer, other= 0;
				for(var i = 1; i < theBody.length; i++)
				{
					//if the parent is the previous object in array, use these positons
				   	if(theBody[i].parent == theBody[i-1])
				   	{
						placer = i-1;
						other = i;
				   	}
				  	
					else
					{	
						//if not we have to find teh parent's position.
						for(var r = 0; r < theWorldBody.length; r++)
								{
									if(theBody[i].parent == theBody[r])
									{

										placer = r;
										other = i;
										//exit the loop
										r = theWorldBody.length;
										
								     }
							    }
	
						}
				   			
				   			//keep track of lengths.		
							var v1 = new THREE.Vector3(theWorldBody[placer].position.x, theWorldBody[placer].position.y, theWorldBody[placer].position.z);
							var v2 = new THREE.Vector3(theWorldBody[other].position.x, theWorldBody[other].position.y, theWorldBody[other].position.z);
							var v3 = new THREE.Vector3(v1.x-v2.x, v1.y - v2.y, v1.z - v2.z); //to find the length
							var v4 = new THREE.Vector3(0, 0, 1); //the axis vector
								
							placementX = placementArray[other].position.x;
							placementY = placementArray[other].position.y;
							placementZ = placementArray[other].position.z;	
							
							//new cylinder object
				   			theLimb = new THREE.Mesh( new THREE.CylinderGeometry( 30, 1.5, 1.5, v3.length() -2 , 1, 1), sphereMaterial );
							
							//keeps track of where to place the limb.
							placementX /= 2;
							placementY /= 2;
							placementZ /= 2;	
						
							//calculations with quaternions for rotation
							v4.normalize();
							v3.normalize();
							var crossVecs = new THREE.Vector3();
							crossVecs.cross(v4,v3);
							crossVecs.normalize();

							var dotVecs = Math.acos(v3.dot(v4)/(v3.length()*v4.length()));

							q1 = new THREE.Quaternion();
							q1.setFromAxisAngle(crossVecs, dotVecs);
							q1.normalize();
	
							if(distance !=0)
							{
						
								theLimb.useQuaternion = true;
								theLimb.quaternion = q1;
								
							
							}
							//sets the position and adds the object to the parent
							theLimb.position.x = placementX;
							theLimb.position.y = placementY;
							theLimb.position.z = placementZ;
							
						
							theArray[num][placer].addChild(theLimb);
					

				}
			
					
			}
			
			//this is so we know how many numbers there will be for each frame.
			var tracker = ((theBody.length-noMovement.length) *3) + 6;
			gcount = 0;
			k = 0;	 
			offset = 0;
			
			//handles the animation
			function animate() {
 
				requestAnimationFrame( animate );
				
				//frame count is found in the file that we read in.
				if(k < frameCount){
					
					
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
						
						//if it is the root it has rotations and positions. we handle this seperately.
						if(joint == 0)
						{
						
							theBody[joint].position.x = movement[offset++];
							theBody[joint].position.z = -movement[offset++];
							theBody[joint].position.y = movement[offset++];
							
							theBody[joint].rotation.x = Math.PI/180 * movement[offset++] + Math.PI /180 * -90;
							theBody[joint].rotation.y = Math.PI/180 * movement[offset++] ;
							theBody[joint].rotation.z = Math.PI/180 * movement[offset++];
						}
				
						//if it's not the root it has three rotations like everything else.
						else
						{	
							theBody[joint].rotation.x = movement[offset++] * Math.PI/180;
							theBody[joint].rotation.y = movement[offset++] * Math.PI/180;
							theBody[joint].rotation.z = movement[offset++] * Math.PI/180;
						}
						
					}
					//calls render function
					if(k % 100 == 0 && k > 99)
					{
						scene.addObject(ghostArray[gcount][0]);
						gcount++;
	
					}
			
					render();	
					k++;
				}
				//if the animation is done, and we ran out of frames. render it as it ended.
				else { 
					render();
				}
			}
 		
			
			//render function
			function render() 
			{
				renderer.render(scene, camera);
				stats.update();
			}
		}
 
		</script> 
	</body> 
</html> 
