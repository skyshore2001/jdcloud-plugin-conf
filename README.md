# 高级系统配置插件

系统默认支持Cinf表做为key-value式的系统配置，但不适用于复杂结构配置项，而且在内部处理时需要取出数据库项解析处理，相对复杂。

本插件支持在前端使用JSON编辑复杂结构配置，在后端支持两种方式保存：

- php配置文件：直接使用程序文件（类文件）作为配置，通过被主程序自动包含而生效，同时允许在前台UI中进行编辑配置。此种方式性能好效率高，但目前只支持单台应用服务器（因为以配置文件方式保存，只在单机上）。
- Cinf表：仍保存在数据库Cinf表中，要求name为`conf_xxx`，当作复杂配置项。通用性好，但在后端使用起来需要读数据库并json解码，略复杂。

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

如果是使用php配置文件来保存，须在conf.php中为配置项指定默认配置值，然后调用JDConf::export指定开放给前端可编辑的配置项数组，示例：
```php
$GLOBALS["conf_pick"] = [ "rules" => [ ] ];
JDConf::export(["conf_pick"]);
```
Cinf表保存方式无须上面步骤，而下面步骤对两种方式均通用。

编写`web/schema/conf_pick.js`文件，描述配置项数据结构、可选值等，详细可参考jdcloud-plugin-jsonEditor插件文档以及该示例文件。

然后在主菜单中添加一项，以二次开发为例，添加一项`高级分拣策略`，配置代码为：

	JDConf("conf_pick")

点击后就可以直接弹出对话框，使用json editor插件来编辑来保存该配置项了。

## 后端接口

使用php配置文件来保存的方式时，须指定开放给前端的配置项数组，配置项名称须为`conf_xxx`这样：

	JDConf::export($confArr);

confArr是个数组，即可以指定多个，示例：

	JDConf::export(["conf_pick"]);

export函数会加载该配置项对应的文件，后端可直接用`getConf("conf_pick")`来取配置项。
配置项`conf_pick`对应的配置文件为`php/conf_pick.php`，注意在迁移时这个文件也须一起备份。

## 交互接口

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

confName为配置项名，以"conf_"开头，如果后端通过`JDConf::export`指定，则直接从全局变量中取值，否则从Cinf表中取值。

