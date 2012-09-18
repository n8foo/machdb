function ajaxObject(layer, url) {                                    // This is the object constructor
   var that=this;                                                    // A workaround for some javascript idiosyncrocies
   var updating = false;                                             // Set to true if this object is already working on a request
   this.callback = function() {}                                     // A post-processing call -- a stub you overwrite.

   this.update = function(passData) {                                // Initiates the server call.
      if (updating==true) { return false; }                          // Abort if we're already processing a call.
      updating=true;                                                 // Set the updating flag.
      var AJAX = null;                                               // Initialize the AJAX variable.
      if (window.XMLHttpRequest) {                                   // Are we working with mozilla?
         AJAX=new XMLHttpRequest();                                  //  Yes -- this is mozilla.
      } else {                                                       // Not Mozilla, must be IE
         AJAX=new ActiveXObject("Microsoft.XMLHTTP");                //  Wheee, ActiveX, how do we format c: again?
      }                                                              // End setup Ajax.
      if (AJAX==null) {                                              // If we couldn't initialize Ajax...
         alert("Your browser doesn't support AJAX.");                // Sorry msg.                                              
         return false                                                // Return false (WARNING - SAME AS ALREADY PROCESSING!)
      } else {
         AJAX.onreadystatechange = function() {                      // When the browser has the request info..
            if (AJAX.readyState==4 || AJAX.readyState=="complete") { //   see if the complete flag is set.
               LayerID.innerHTML=AJAX.responseText;                  //   It is, so put the new data in the object's layer
               delete AJAX;                                          //   delete the AJAX object since it's done.
               updating=false;                                       //   Set the updating flag to false so we can do a new request
               that.callback();                                      //   Call the post-processing function.
            }                                                        // End Ajax readystate check.
         }                                                           // End create post-process fucntion block.
         var timestamp = new Date();                                 // Get a new date (this will make the url unique)
         var uri=urlCall+'?'+passData+'&timestamp='+(timestamp*1);   // Append date to url (so the browser doesn't cache the call)
         AJAX.open("GET", uri, true);                                // Open the url this object was set-up with.
         AJAX.send(null);                                            // Send the request.
         return true;                                                // Everything went a-ok.
      }                                                              // End Ajax setup aok if/else block                 
   }
      
   // This area set up on constructor calls.
   var LayerID = document.getElementById(layer);                     // Remember the layer associated with this object.
   var urlCall = url;                                                // Remember the url associated with this object.
}                                                                    // End AjaxObject

