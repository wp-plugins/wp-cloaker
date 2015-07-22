jQuery(document).ready(function($){
	ZeroClipboard.config( { swfPath: wpCloaker.url + "assets/js/ZeroClipboard.swf" } );
	var client = new ZeroClipboard( $('.copy') );

      client.on( 'ready', function(event) {
        // console.log( 'movie is loaded' );

        client.on( 'copy', function(event) {
          event.clipboardData.setData('text/plain', event.target.getAttribute('datalink'));
        } );

        client.on( 'aftercopy', function(event) {
         // console.log('Copied text to clipboard: ' + event.data['text/plain']);
        } );
      } );

      client.on( 'error', function(event) {
        // console.log( 'ZeroClipboard error of type "' + event.name + '": ' + event.message );
        ZeroClipboard.destroy();
      } );
});


