<html>
    <head>
        <script src="d3.v3.min.js" charset="utf-8"></script>
        <link rel="stylesheet" type="text/css" href="graaferi.css">
        <title>My email statistics (pointed out by Jesse Luoto)</title>
        <script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
    </head>
    <body>
        <script id="data" type="text/text"><?php include 'data.txt'; ?></script>
        <script>
            function Round(value, rounder) {
                if(rounder >= 1) return Math.round(value / rounder) * rounder;
                
                rounder = 1/rounder;
                return Math.round(value * rounder) / rounder;
            }
            
            $(function() {
                var ONE_HOUR = (1000 * 60 * 60);
                var ONE_DAY = ONE_HOUR * 24;
                var ONE_WEEK = ONE_DAY * 7;
                var MAX_HOURS = 7 * 24;
                
                function GetHourID(date) {
                    return date.getDay() * 24 + date.getHours();
                }
                
                function HourStamp(ID) {
                    this.val = 0;
                    this.ID = ID;
                    this.GetNext = function() {
                        return hours[ID+1];
                    }
                    this.GetPrev = function() {
                        return hours[ID-1];
                    }
                    this.LerpVal = function(timestamp) {
                        var nextVal = this.GetNext().val;
                        
                        var stampTime = new Date(timestamp);
                        
                        var perc = stampTime.getMinutes() / 60;
                        
                        return (this.val - nextVal) * perc + this.val;
                    }
                    this.GetMillisecondsFromSunday = function() {
                        return ID * ONE_HOUR;
                    }
                }
                
                
                var data = $("#data").html().split("\n");
                
                var hours = [];
                for(var i = 0; i <= MAX_HOURS; i++) {
                    hours.push(new HourStamp(i));
                }
                
                var first = 0;
                var last = 0;
                var maxVal = 0;
                
                for(var i = 0; i < data.length; i++) {
                    var date = new Date(data[i]);
                    var time = date.getTime();
                    
                    if(!first || time < first) first = time;
                    if(!last || time > last) last = time;
                    
                    var hour = GetHourID(date);
                    if(hours[hour]) {
                        hours[hour].val++;
                        if(hours[hour].val > maxVal) maxVal = hours[hour].val;
                    }
                }
                
                var max_days = Math.floor((last-first)/ONE_WEEK);
                
                function Refresh() {
                    var inspectTime = new Date();
                    //inspectTime.setHours(12);
                    
                    var hourStamp = hours[GetHourID(inspectTime)];
                    var mailsPerHour = hourStamp.LerpVal(inspectTime.getTime()) / max_days;
                    
                    $("#propability strong").text(Round(mailsPerHour*100, 0.1) + "%")
                    //console.log("Propability of getting email this hour: " + Round(mailsPerHour*100, 0.1) + "%");
                    $("#send strong").text(mailsPerHour < 2 ? "Propably" : "no.")
                    
                    var hue = parseInt(Math.max(0, 100 - (50 * mailsPerHour)));
    
                    $("body").css("background-color", "hsl("+(hue)+",54%,45%)")
                    
                    if(mailsPerHour > 1) {
                        var mailsPerMinute = mailsPerHour / 60;
                        var minutesPerMail = 1/mailsPerMinute;
                        //console.log("You're supposed to get avg of "+ Round(mailsPerMinute,0.1)+" mails per minute");
                        //console.log("That makes about one mail per " + Round(minutesPerMail, 1) + " minutes");
                        
                        $("#average strong span").text(Round(minutesPerMail, 1));
                        $("#average small").text(" minutes");
                    } else {
                        var hoursPerMail = 1 / mailsPerHour;
                        //console.log("That makes about one mail per " + Round(hoursPerMail, 0.1) + " hours");
                        
                        
                        $("#average strong span").text(Round(hoursPerMail, 0.1));
                        $("#average small").text(" hours");
                    }
                }
                Refresh();
                
                setInterval(Refresh,1000);
                
                
            
                var w = 1100;
                var h = 280;
                var p = 20;
                
                var svg = d3.select("#svg").append("svg")
                            .attr("width", w)
                            .attr("height", h + p*2)
                
                
                var x = d3.time.scale()
                                .range([0, w]);
                var y = d3.scale.linear()
                                .range([h, 0]);
                                
                var line = d3.svg.line()
                                .x(function(d,i) { return x(i/MAX_HOURS); })
                                .y(function(d) { return y(d.val / maxVal); })
                                
                var g = svg.append("g")
                           .attr("transform", "translate(0, "+p+"px)");
                
                g.append("svg:path")
                 .attr("d", line(hours))
                
                
                var lineNow = g.append("svg:line")
                               .attr("class", "line-now")
                
                function RefreshSVG() {
                    var currentID = GetHourID( new Date() );
                    lineNow.attr("x1", x(currentID/MAX_HOURS))
                           .attr("y1", y(1))
                           .attr("x2", x(currentID/MAX_HOURS))
                           .attr("y2", y(0))
                }
                RefreshSVG();
                setInterval(RefreshSVG, 10000);
                
                g.append("svg:line")
                 .attr("class", "line-limit")
                 .attr("x1", x(0))
                 .attr("y1", y((max_days*2)/maxVal))
                 .attr("x2", x(1))
                 .attr("y2", y((max_days*2)/maxVal))
            });
        </script>
        <h1>My email statistics</h1>
        <div id="stats">
            <div class="row">
                <div id="propability" class="box">
                    Propability of getting email in an hour:
                    <strong></strong>
                </div>
                <div id="average" class="box">
                    That makes about one mail per
                    <strong><span></span><small></small></strong> 
                </div>
                <div id="send" class="box">
                    So could you send me an email?
                    <strong></strong> 
                </div>
            </div>
        </div>
        <div id="svg">
        </div>
        <div id="footer">
            <h3>Info:</h3>
            <p>Actual data parsed from Thunderbird's mail logs.</p>
            <p>Point being, that over a certain limit (2 emails per hour) you can't really concentrate on your work anymore, because it takes up to 20 minutes to get the flow back on after you recieve and read an just-arrived email.</p>
        </div>
    </body>
</html>