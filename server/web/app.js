function JDConf(name)
{
	callSvr("JDConf.get", {name: name}, function (data) {
		DlgJson.show("schema/" + name + ".js", data, onSetJson, {modal: false});
	});
	
	function onSetJson (data) {
		callSvr("JDConf.set", {name: name}, function () {
			app_show("更新成功");
		}, data);
	}
}
