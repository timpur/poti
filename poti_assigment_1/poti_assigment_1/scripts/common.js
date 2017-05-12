/// <reference path="jquery-3.2.1.min.js" />

function callPHP(url, data, callback) {
	jQuery.ajax({
		url: url,
		data: JSON.stringify(data),
		method: "POST",
		success: callback,
		dataType: "json",
		contentType:"application/json"
	});
}