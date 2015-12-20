<html>
    <head>
        <script src="http://d3js.org/d3.v3.min.js" charset="utf-8"></script>
        <script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
        <style>

        body {
          font: 10px sans-serif;
        }
        
        .axis path,
        .axis line {
          fill: none;
          stroke: #000;
          shape-rendering: crispEdges;
        }
        
        .bar {
          fill: steelblue;
        }
        
        .x.axis path {
          display: none;
        }
        .chart div {
            font: 10px sans-serif;
            background-color: steelblue;
            text-align: right;
            padding: 3px;
            margin: 1px;
            color: white;
        }
        .chart {
            margin: 20px;
        }
        
        
        
        </style>
    </head>
    <body>
        <script id="data" type="text/text"><?php include 'data.txt'; ?></script>
        <script>
            $(function() {
                var data = $("#data").html().split("\n");
                var weekdaytmp = {};
                var hourlytmp = {};
                var weekhourtmp = {};
                
                for(var i = 0; i < data.length; i++) {
                    var line = data[i];
                    var weekday = line.substr(0,3);
                    var hour = new Date(line).getHours();
                    
                    
                    weekdaytmp[weekday] = weekdaytmp[weekday]+1 || 1;
                    hourlytmp[hour] = hourlytmp[hour]+1 || 1;
                    
                    weekhourtmp[weekday] = weekhourtmp[weekday] || {};
                    weekhourtmp[weekday][hour] = weekhourtmp[weekday][hour]+1 || 1;
                }
                var weekdays = [];
                for(var i in weekdaytmp) {
                    weekdays.push({
                        label: i,
                        value: weekdaytmp[i]
                    });
                }
                var hourly = [];
                for(var i in hourlytmp) {
                    if(!parseInt(i)) continue;
                    hourly.push({
                        label: i+"-"+(parseInt(i)+1)+":00",
                        value: hourlytmp[i]
                    });
                }
                var weekhourly = {};
                for(var i in weekhourtmp) {
                    weekhourly[i] = weekhourly[i] || [];
                    
                    
                    for(var j in weekhourtmp[i]) {
                        if(!parseInt(j)) continue;
                        weekhourly[i].push({
                            label: j+"-"+(parseInt(j)+1)+":00",
                            value: weekhourtmp[i][j]
                        });
                    }
                }
                
                /*
                
                var chart1 = d3.select("body").append("div").attr("class", "chart").append("h3").text("Weekly data");
                var chart2 = d3.select("body").append("div").attr("class", "chart").append("h3").text("Hourly data");
                
                chart1.selectAll("div")
                    .data(weekdays)
                    .enter().append("div")
                    .style("width", function(d) { return d.value * 3 + "px"; })
                    .text(function(d) { return d.wd; });
                
                chart2.selectAll("div")
                    .data(hourly)
                    .enter().append("div")
                    .style("width", function(d) { return d.value * 3 + "px"; })
                    .text(function(d) { return d.wd+":00-"+(parseInt(d.wd)+1)+":00"; });
                
                /**/
                
                function CreateDataset(dataset, name) {
                    var w = 1100;
                    var h = 300;
                    
                    var max = 0;
                    for(var i in dataset) {
                        max = Math.max(max, dataset[i].value);
                    }
                    
                    d3.select("body")
                        .append("h2")
                        .text(name)
                        
                    var svg = d3.select("body")
                                .append("svg")
                                .attr("width", w)
                                .attr("height", h);
                    
                                
                    
                    svg.selectAll("rect")
                        .data(dataset)
                        .enter()
                        .append("rect")
                        .attr("x", function(d, i) {
                            return i * (w / dataset.length);  //Bar width of 20 plus 1 for padding
                        })
                        .attr("y", function(d) {
                            return h - d.value / max * (h - 10) - 10;
                        })
                        .attr("width", function() {
                            return w / dataset.length - 2;
                        })
                        .attr("height", function(d) {
                            return d.value / max * (h - 10);
                        });
                    
                    svg.selectAll("text")
                        .data(dataset)
                        .enter()
                        .append("text")
                        .text(function(d) {
                            return d.label;
                        })
                        .attr("x", function(d, i) {
                            return i * (w / dataset.length);  //Bar width of 20 plus 1 for padding
                        })
                        .attr("y", function(d) {
                            return h; //h - d.value / max * h;
                        })
                    
                    d3.select("body")
                        .append("hr")
                        
                }
                //CreateDataset(hourly);
                for(var i in weekhourly) {
                    CreateDataset(weekhourly[i], i);
                }
            });
        </script>
        <h1>Jesse's email distribution</h1>
        <pre id="output" style="width: 600px; height: 1000px; border: 3px dotted #000; padding: 10px; overflow: scroll; display: none;">
        </pre>
    </body>
</html>