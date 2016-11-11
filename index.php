<?php
if(isset($_GET['cmd'])) {
    echo file_get_contents("cmd/" . $_GET['cmd'] . ".txt");
    exit;
}
elseif (isset($_GET['newCmd'])) {
    echo $_GET['newCmd'];
    file_put_contents("new.index", $_GET['newCmd'] . PHP_EOL, FILE_APPEND);
    exit;
}
?>

<html>
<head>
    <title>Shell命令在线速查手册</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" />
    <style>
        input, .content {
            margin: 10px 0;
        }

        .content {
            border-left: 1px solid;
        }

        .cmd_list {
            max-height: 100%;
            overflow-y: scroll;
        }

        .content tr th:first {
            white-space: nowrap;
        }
    </style>
</head>

<body ng-app="app">
    <div class="container" ng-controller="cmdCtrl">
        <div class="row">
            <div class="col-md-4">
                <div class="cmd_list">
                    <input class="form-control" ng-change="filter()" type="text" ng-model="keyword" placeholder="请输入命令进行查找"/>

                    <ol>
                        <li ng-repeat="cmd in cmdList" ng-class="{true: '', false: 'hide'}[cmd.show]" ng-click="show(cmd)">
                            {{cmd.name}}
                            <p>{{cmd.desc}}</p>
                        </li>
                    </ol>
                </div>
            </div>
            <div class="col-md-8 content">
                <div ng-hide="content != null && content != ''">
                    <p>请在左侧搜索命令
                    <p>
                    <p>
                    <p>如果命令没有搜索到，请提交命令，我会及时收录
                    <input type="text" class="form-control" style="width: 30%" placeholder="请输入您未找到的命令" ng-model="newCmd"/>
                    <button class="btn btn-success" ng-click="newCmdSubmit()">提交</button>
                </div>
                <div ng-bind-html="content" ng-show="content != null && content != ''">

                </div>
            </div>
        </div>
    </div>

    <footer style="text-align:center;margin:10px auto">
        support by calvin q383846707
    </footer>

    <script src="jQuery/jquery-2.2.3.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="angular/angular.min.js"></script>
    <script src="angular/angular-sanitize.min.js"></script>
    <script>
        angular.module("app", ['ngSanitize'])
            .controller("cmdCtrl", function($scope, $http, $timeout) {

                $scope.newCmdSubmit = function() {
                    if($scope.newCmd == null || $scope.newCmd == "") {
                            alert("请输入");
                            return ;
                    }
                    $http({
                        url: "?newCmd=" + $scope.newCmd,
                        method: "GET"
                    }).success(function(ret) {
                        $scope.newCmd = null;
                        alert("提交成功，感谢您的贡献");
                    });
                };

                $scope.currentCmd = null;
                $scope.selectedCmd = "";

                function show() {
                    if($scope.selectedCmd == $scope.currentCmd) {
                        return;
                    }

                    $scope.currentCmd = $scope.selectedCmd;

                    $http({
                        url : "?cmd=" + $scope.selectedCmd,
                        method : "GET"
                    }).success(function(ret) {
                        $scope.content = ret;
                        $timeout(function() {
                            $(".content tr th:first").width("50px");
                            $(".content table").addClass("table table-hover");
                        }, 500);
                    });
                }

                $scope.show = function(cmd) {
                    $scope.selectedCmd = cmd.name;

                    show();
                };

                $scope.filter = function() {
                    if($scope.keyword == null || $scope.keyword == "") {
                        for(var x in $scope.cmdList) {
                            $scope.cmdList[x].show = "true";
                        }
                        $scope.content = "";
                        $scope.currentCmd = null;
                        return;
                    }

                    var showCount = 0;
                    for(var x in $scope.cmdList) {
                        $scope.cmdList[x].show = $scope.cmdList[x].name.indexOf($scope.keyword) >= 0 ? true : false;
                        if($scope.cmdList[x].show) {
                            showCount ++;
                            $scope.selectedCmd = $scope.cmdList[x].name;
                        }
                    }

                    if(showCount == 1) {
                        console.log($scope.selectedCmd);

                        show();
                    }

                };

                $scope.cmdList = [<?php
                    $count = 0;
                    $file = file("cmd.index");
                    foreach($file as &$line) {
                        if($count > 0) {
                            echo ",";
                        }
                        $array = explode("$", $line);
                        echo "{show:true, name:'$array[0]', desc: '" . str_replace("\n", "", $array[1]) . "'}";
                        $count ++;
                    }
                ?>];
            });
    </script>
</body>
</html>
