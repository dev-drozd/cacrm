<script src="https://raw.githubusercontent.com/peers/peerjs/master/dist/peer.min.js"></script>
<script>
var peer = new Peer();
var conn = peer.connect('user-{user-id}');
conn.on('open', function(){
  conn.send('hi!');
});

peer.on('connection', function(conn) {
  conn.on('data', function(data){
    console.log(data);
  });
});

var getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia;
peer.on('call', function(call) {
  getUserMedia({video: true, audio: true}, function(stream) {
    call.answer(stream);
    call.on('stream', function(remoteStream) {
	console.log(remoteStream);
    });
  }, function(err) {
    console.log('Failed to get local stream' ,err);
  });
});

var to_call = function(a){
	getUserMedia({video: true, audio: true}, function(stream) {
	  var call = peer.call('another-peers-id', stream);
	  call.on('stream', function(remoteStream) {
	  console.log(remoteStream);
	  });
	}, function(err) {
	  console.log('Failed to get local stream' ,err);
	});
};
</script>