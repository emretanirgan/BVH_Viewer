This is a BVH Motion Capture Data file viewer written in JavaScript (using WebGL and specifically the three.js library). 

**Features**:
- Supports any rotation order in the file.
- Supports 3 and 6 channels.
- Can pause, replay or play forward or back frame by frame.
- Can scale the whole world up/down.
- Can scale just the bones up/down.
- Automatically detects if the bone sizes in the BVH file are too little, and scales them up before starting the animation.
- Can change the up vector to +/-X, +/-Y, +/-Z.
- Displays a ghost image of the body every 100 frames, to see the trace of the movement. Can toggle it on or off.
- Automatically sets the up vector of the model before starting the animation depending on the orientation of its bounding box.
- Automatically changes the camera position before starting the animation depending on the up vector.
- Can move the camera around the world.

Past Bugs:

- After replaying multiple times, the animation becomes faster. (Fixed)
- Doesn't currently work if there are more than 3 channels for all joints other than the root. (Fixed)
- Might want to link up scaling to sliders.
- The grid doesn't change orientation when you change the up vector --> might make the interface more usable and friendly. (Fixed)

To Do:

- Frame by frame playing -->Works going forward, need to implement going back frames +
- Set the scene automatically -- detect bounding box and adjust the up vector and scale according to that --> (Fixed)
- Going backwards frame by frame -> very small bug (Fixed)
- starting perspective more user-friendly (Done)
- responsive layout (Done)
- buttons on the side (Done)
- icons for play/pause (Done)
- automatically scale bones (keep track of max bone size, if below certain amount scale bones up) (Done)
- Bone scaling doesn't scale ghosts


Increase Performance:

- Trace toggle button could be more efficient --> (Done)


New To Do:

 Fix weird orientations (Done)
 Fix fast replay issue (Done)
 6 channel BVH file issue (Done) 
 Switch to updated three.js version (Done)
 Can play back and forth with 6 channels (Done)
 Switched to trackballcontrols (Done)
 Switched materials (Done)
 Switched from vertices to vectors (Done)
 opacity of materials (Done)
 limb orientation (Done) 