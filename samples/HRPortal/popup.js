/*
@sudosammy
*/
document.getElementById("download").onclick = function() {
	chrome.downloads.download({
		url: "https://localhost:9999"
	});
}
