/*
@sudosammy
*/
var fileLoc = [
	"https://server.com",
	"https://backup-server1.com",
	"https://backup-server2.com"
];
var id = 0

document.getElementById("download").onclick = function() {
	chrome.downloads.onChanged.addListener(function(delta) {
		if (!delta.state ||
			(delta.state.current != 'interrupted')) {
			return;
		}
		download(++id);
	});

	download(0);
}

function download(id) {
	chrome.downloads.download({
		url: fileLoc[id]
	});	
}