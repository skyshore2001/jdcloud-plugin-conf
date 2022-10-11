# 高级系统配置插件

系统默认支持Cinf表做为key-value式的系统配置，但不适用于复杂结构配置项，而且在内部处理时需要取出数据库项解析处理，相对复杂。

本插件直接使用程序文件（类文件）作为配置，通过被主程序自动包含而生效，同时允许在前台UI中进行编辑配置。

本插件依赖以下插件：

- jdcloud-plugin-jsonEditor

## 用法

需求示例：

轮胎修剪工艺分拣系统中，需要配置特殊轮胎去专门的修剪机台，即如果轮胎的属性1满足某条件，且属性2满足某条件（支持多个条件组合），则去某分拣机台。

设计配置项样例如下：

```php
$GLOBALS["conf_pick"] = [
	"rules" => [
		[
			// 分拣条件
			"cond" => [
				["name"=>"prop1", "value"=>"value1"],
				["name"=>"prop2", "value"=>">100"],
				["name"=>"prop3", "value"=>"100-200"],
			],
			// 分拣口编号
			"station" => "P3"
		]
	]
];
```

为了实现前端对该配置项的修改，可引入本插件，采用如下方法。

在conf.php中为配置项指定默认配置值，然后调用JDConf::export指定开放给前端可编辑的配置项数组，示例：
```php
$GLOBALS["conf_pick"] = [ "rules" => [ ] ];
JDConf::export(["conf_pick"]);
```

编写`web/schema/conf_pick.js`文件，描述配置项数据结构、可选值等，详细可参考jdcloud-plugin-jsonEditor插件文档以及该示例文件。

然后在主菜单中添加一项，以二次开发为例，添加一项`高级分拣策略`，配置代码为：

	JDConf("conf_pick")

点击后就可以直接弹出对话框，使用json editor插件来编辑来保存该配置项了。

## 后端接口

指定开放给前端的配置项：

	JDConf::export($confArr)

confArr是个数组，即可以指定多个；同时加载该配置项对应的文件，例如`conf_pick`对应`php/conf_pick.php`文件。

## 交互接口

权限：仅当JDConf::export中指定的配置项才可以读写。

	JDConf.get(name)
	JDConf.set(name)(value...)

- JDConf.set接口会将配置写入到文件php/conf_pick.php中。

示例：读配置项conf_pick

	callSvr("JDConf.get", {name:"conf_pick"})

示例：写配置项conf_pick

	callSvr("JDConf.set", {name: "conf_pick"}, $.noop, {
		rules: [
			{
				cond: {
					{name:"prop1", value:"value1"},
					{name:"prop2", value:">100"},
					{name:"prop3", value:"100-200"}
				}
				station: "P3"
			}
		]
	});

## 前端接口

打开对话框编辑某配置项：

	JDConf(confName)

confName为配置项名，以"conf_"开头，必须在后端通过`JDConf::export`开放出来才可以用。

