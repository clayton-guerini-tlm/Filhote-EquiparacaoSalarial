// THIS FILE HAS BEEN MINIFIED

if(typeof(RGraph) == 'undefined') RGraph = {};RGraph.HBar = function (id, data)
{
this.id = id;this.canvas = document.getElementById(id);this.context = this.canvas.getContext ? this.canvas.getContext("2d") : null;this.canvas.__object__ = this;this.data = data;this.type = 'hbar';this.coords = [];this.isRGraph = true;this.uid = RGraph.CreateUID();RGraph.OldBrowserCompat(this.context);this.max = 0;this.stackedOrGrouped = false;this.properties = {
'chart.gutter.left':            75,
'chart.gutter.right':           25,
'chart.gutter.top':             35,
'chart.gutter.bottom':          25,
'chart.background.grid':        true,
'chart.background.grid.color':  '#ddd',
'chart.background.grid.width':  1,
'chart.background.grid.hsize':  25,
'chart.background.grid.vsize':  25,
'chart.background.barcolor1':   'white',
'chart.background.barcolor2':   'white',
'chart.background.grid.hlines': true,
'chart.background.grid.vlines': true,
'chart.background.grid.border': true,
'chart.background.grid.autofit':true,
'chart.background.grid.autofit.numhlines': 14,
'chart.background.grid.autofit.numvlines': 20,
'chart.title':                  '',
'chart.title.background':       null,
'chart.title.xaxis':            '',
'chart.title.xaxis.bold':       true,
'chart.title.xaxis.size':       null,
'chart.title.xaxis.font':       null,
'chart.title.yaxis':            '',
'chart.title.yaxis.bold':       true,
'chart.title.yaxis.size':       null,
'chart.title.yaxis.font':       null,
'chart.title.xaxis.pos':        null,
'chart.title.yaxis.pos':        10,
'chart.title.hpos':             null,
'chart.title.vpos':             null,
'chart.title.bold':             true,
'chart.title.font':             null,
'chart.text.size':              10,
'chart.text.color':             'black',
'chart.text.font':              'Arial',
'chart.colors':                 ['red', 'blue', 'green', 'pink', 'yellow', 'cyan', 'navy', 'gray', 'black'],
'chart.labels':                 [],
'chart.labels.above':           false,
'chart.labels.above.decimals':  0,
'chart.xlabels':                true,
'chart.contextmenu':            null,
'chart.key':                    [],
'chart.key.background':         'white',
'chart.key.position':           'graph',
'chart.key.halign':             'right',
'chart.key.shadow':             false,
'chart.key.shadow.color':       '#666',
'chart.key.shadow.blur':        3,
'chart.key.shadow.offsetx':     2,
'chart.key.shadow.offsety':     2,
'chart.key.position.gutter.boxed': true,
'chart.key.position.x':         null,
'chart.key.position.y':         null,
'chart.key.color.shape':        'square',
'chart.key.rounded':            true,
'chart.key.linewidth':          1,
'chart.key.colors':             null,
'chart.units.pre':              '',
'chart.units.post':             '',
'chart.units.ingraph':          false,
'chart.strokestyle':            'black',
'chart.xmin':                   0,
'chart.xmax':                   0,
'chart.axis.color':             'black',
'chart.shadow':                 false,
'chart.shadow.color':           '#666',
'chart.shadow.blur':            3,
'chart.shadow.offsetx':         3,
'chart.shadow.offsety':         3,
'chart.vmargin':                3,
'chart.grouping':               'grouped',
'chart.tooltips':               null,
'chart.tooltips.event':         'onclick',
'chart.tooltips.effect':        'fade',
'chart.tooltips.css.class':     'RGraph_tooltip',
'chart.tooltips.highlight':     true,
'chart.highlight.fill':         'rgba(255,255,255,0.7)',
'chart.highlight.stroke':       'rgba(0,0,0,0)',
'chart.annotatable':            false,
'chart.annotate.color':         'black',
'chart.zoom.factor':            1.5,
'chart.zoom.fade.in':           true,
'chart.zoom.fade.out':          true,
'chart.zoom.hdir':              'right',
'chart.zoom.vdir':              'down',
'chart.zoom.frames':            25,
'chart.zoom.delay':             16.666,
'chart.zoom.shadow':            true,
'chart.zoom.mode':              'canvas',
'chart.zoom.thumbnail.width':   75,
'chart.zoom.thumbnail.height':  75,
'chart.zoom.thumbnail.fixed':   false,
'chart.zoom.background':        true,
'chart.zoom.action':            'zoom',
'chart.resizable':              false,
'chart.resize.handle.adjust':   [0,0],
'chart.resize.handle.background': null,
'chart.scale.point':            '.',
'chart.scale.thousand':         ',',
'chart.scale.decimals':         null,
'chart.noredraw':               false,
'chart.events.click':           null,
'chart.events.mousemove':       null
}
if(!this.canvas){
alert('[HBAR] No canvas support');return;}
for (i=0; i<this.data.length; ++i){
if(typeof(this.data[i]) == 'object'){
this.stackedOrGrouped = true;}
}
this.data_arr = RGraph.array_linearize(this.data);cl(this.data_arr);this.getShape = this.getBar;RGraph.Register(this);}
RGraph.HBar.prototype.Set = function (name, value)
{
if(name == 'chart.labels.abovebar'){
name = 'chart.labels.above';}
this.properties[name.toLowerCase()] = value;}
RGraph.HBar.prototype.Get = function (name)
{
if(name == 'chart.labels.abovebar'){
name = 'chart.labels.above';}
return this.properties[name];}
RGraph.HBar.prototype.Draw = function ()
{
RGraph.FireCustomEvent(this, 'onbeforedraw');this.gutterLeft = this.Get('chart.gutter.left');this.gutterRight = this.Get('chart.gutter.right');this.gutterTop = this.Get('chart.gutter.top');this.gutterBottom = this.Get('chart.gutter.bottom');this.coords = [];this.max = 0;if(this.Get('chart.xmin') > 0 && this.Get('chart.grouping') == 'stacked'){
alert('[HBAR] Using chart.xmin is not supported with stacked charts, resetting chart.xmin to zero');this.Set('chart.xmin', 0);}
this.graphwidth = this.canvas.width - this.gutterLeft - this.gutterRight;this.graphheight = this.canvas.height - this.gutterTop - this.gutterBottom;this.halfgrapharea = this.grapharea / 2;this.halfTextHeight = this.Get('chart.text.size') / 2;RGraph.background.Draw(this);this.Drawbars();this.DrawAxes();this.DrawLabels();if(this.Get('chart.key').length){
RGraph.DrawKey(this, this.Get('chart.key'), this.Get('chart.colors'));}
RGraph.InstallUserClickListener(this, this.Get('chart.events.click'));RGraph.InstallUserMousemoveListener(this, this.Get('chart.events.mousemove'));RGraph.AllowTooltips(this);if(this.Get('chart.contextmenu')){
RGraph.ShowContext(this);}
RGraph.DrawInGraphLabels(this);if(this.Get('chart.annotatable')){
RGraph.Annotate(this);}
if(this.Get('chart.zoom.mode') == 'thumbnail' || this.Get('chart.zoom.mode') == 'area'){
RGraph.ShowZoomWindow(this);}
if(this.Get('chart.resizable')){
RGraph.AllowResizing(this);}
RGraph.FireCustomEvent(this, 'ondraw');}
RGraph.HBar.prototype.DrawAxes = function ()
{
var halfway = (this.graphwidth / 2) + this.gutterLeft;this.context.beginPath();this.context.lineWidth = 1;this.context.strokeStyle = this.Get('chart.axis.color');if(this.Get('chart.yaxispos') == 'center'){
this.context.moveTo(AA(this, halfway), this.gutterTop);this.context.lineTo(AA(this, halfway), this.canvas.height - this.gutterBottom);} else {
this.context.moveTo(AA(this, this.gutterLeft), this.gutterTop);this.context.lineTo(AA(this, this.gutterLeft), this.canvas.height - this.gutterBottom);}
this.context.moveTo(this.gutterLeft, AA(this, RGraph.GetHeight(this) - this.gutterBottom));this.context.lineTo(this.canvas.width - this.gutterRight, AA(this, this.canvas.height - this.gutterBottom));var yTickGap = (this.canvas.height - this.gutterTop - this.gutterBottom) / this.data.length;for (y=this.gutterTop; y<(this.canvas.height - this.gutterBottom - 1); y+=yTickGap){
if(this.Get('chart.yaxispos') == 'center'){
this.context.moveTo(halfway + 3, AA(this, y));this.context.lineTo(halfway  - 3, AA(this, y));} else {
this.context.moveTo(this.gutterLeft, AA(this, y));this.context.lineTo( this.gutterLeft  - 3, AA(this, y));}
}
xTickGap = (this.canvas.width - this.gutterLeft - this.gutterRight ) / 10;yStart = this.canvas.height - this.gutterBottom;yEnd = (this.canvas.height - this.gutterBottom) + 3;for (x=(this.canvas.width - this.gutterRight), i=0; this.Get('chart.yaxispos') == 'center' ? x>=this.gutterLeft : x>this.gutterLeft; x-=xTickGap){
if(this.Get('chart.yaxispos') != 'center' || i != 5){
this.context.moveTo(AA(this, x), yStart);this.context.lineTo(AA(this, x), yEnd);}
i++;}
this.context.stroke();}
RGraph.HBar.prototype.DrawLabels = function ()
{
var context = this.context;var canvas = this.canvas;var units_pre = this.Get('chart.units.pre');var units_post = this.Get('chart.units.post');var text_size = this.Get('chart.text.size');var font = this.Get('chart.text.font');if(this.Get('chart.units.ingraph')){
units_pre = '';units_post = '';}
if(this.Get('chart.xlabels')){
var gap = 5;this.context.beginPath();this.context.fillStyle = this.Get('chart.text.color');if(this.Get('chart.yaxispos') == 'center'){
RGraph.Text(context, font, text_size, this.gutterLeft + (this.graphwidth * (10/10)), this.gutterTop + this.halfTextHeight + this.graphheight + gap, RGraph.number_format(this, Number(this.scale[4]).toFixed(this.Get('chart.scale.decimals')), units_pre, units_post), 'center', 'center');RGraph.Text(context, font, text_size, this.gutterLeft + (this.graphwidth * (9/10)), this.gutterTop + this.halfTextHeight + this.graphheight + gap, RGraph.number_format(this, Number(this.scale[3]).toFixed(this.Get('chart.scale.decimals')), units_pre, units_post), 'center', 'center');RGraph.Text(context, font, text_size, this.gutterLeft + (this.graphwidth * (8/10)), this.gutterTop + this.halfTextHeight + this.graphheight + gap, RGraph.number_format(this, Number(this.scale[2]).toFixed(this.Get('chart.scale.decimals')), units_pre, units_post), 'center', 'center');RGraph.Text(context, font, text_size, this.gutterLeft + (this.graphwidth * (7/10)), this.gutterTop + this.halfTextHeight + this.graphheight + gap, RGraph.number_format(this, Number(this.scale[1]).toFixed(this.Get('chart.scale.decimals')), units_pre, units_post), 'center', 'center');RGraph.Text(context, font, text_size, this.gutterLeft + (this.graphwidth * (6/10)), this.gutterTop + this.halfTextHeight + this.graphheight + gap, RGraph.number_format(this, Number(this.scale[0]).toFixed(this.Get('chart.scale.decimals')), units_pre, units_post), 'center', 'center');RGraph.Text(context, font, text_size, this.gutterLeft + (this.graphwidth * (4/10)), this.gutterTop + this.halfTextHeight + this.graphheight + gap, '-' + RGraph.number_format(this, Number(this.scale[0]).toFixed(this.Get('chart.scale.decimals')), units_pre, units_post), 'center', 'center');RGraph.Text(context, font, text_size, this.gutterLeft + (this.graphwidth * (3/10)), this.gutterTop + this.halfTextHeight + this.graphheight + gap, '-' + RGraph.number_format(this, Number(this.scale[1]).toFixed(this.Get('chart.scale.decimals')), units_pre, units_post), 'center', 'center');RGraph.Text(context, font, text_size, this.gutterLeft + (this.graphwidth * (2/10)), this.gutterTop + this.halfTextHeight + this.graphheight + gap, '-' + RGraph.number_format(this, Number(this.scale[2]).toFixed(this.Get('chart.scale.decimals')), units_pre, units_post), 'center', 'center');RGraph.Text(context, font, text_size, this.gutterLeft + (this.graphwidth * (1/10)), this.gutterTop + this.halfTextHeight + this.graphheight + gap, '-' + RGraph.number_format(this, Number(this.scale[3]).toFixed(this.Get('chart.scale.decimals')), units_pre, units_post), 'center', 'center');RGraph.Text(context, font, text_size, this.gutterLeft + (this.graphwidth * (0)), this.gutterTop + this.halfTextHeight + this.graphheight + gap, '-' + RGraph.number_format(this, Number(this.scale[4]).toFixed(this.Get('chart.scale.decimals')), units_pre, units_post), 'center', 'center');} else {
RGraph.Text(context, font, text_size, this.gutterLeft + (this.graphwidth * (5/5)), this.gutterTop + this.halfTextHeight + this.graphheight + gap, RGraph.number_format(this, Number(this.scale[4]).toFixed(this.Get('chart.scale.decimals')), units_pre, units_post), 'center', 'center');RGraph.Text(context, font, text_size, this.gutterLeft + (this.graphwidth * (4/5)), this.gutterTop + this.halfTextHeight + this.graphheight + gap, RGraph.number_format(this, Number(this.scale[3]).toFixed(this.Get('chart.scale.decimals')), units_pre, units_post), 'center', 'center');RGraph.Text(context, font, text_size, this.gutterLeft + (this.graphwidth * (3/5)), this.gutterTop + this.halfTextHeight + this.graphheight + gap, RGraph.number_format(this, Number(this.scale[2]).toFixed(this.Get('chart.scale.decimals')), units_pre, units_post), 'center', 'center');RGraph.Text(context, font, text_size, this.gutterLeft + (this.graphwidth * (2/5)), this.gutterTop + this.halfTextHeight + this.graphheight + gap, RGraph.number_format(this, Number(this.scale[1]).toFixed(this.Get('chart.scale.decimals')), units_pre, units_post), 'center', 'center');RGraph.Text(context, font, text_size, this.gutterLeft + (this.graphwidth * (1/5)), this.gutterTop + this.halfTextHeight + this.graphheight + gap, RGraph.number_format(this, Number(this.scale[0]).toFixed(this.Get('chart.scale.decimals')), units_pre, units_post), 'center', 'center');if(this.Get('chart.xmin') > 0){
RGraph.Text(context,font,text_size,this.gutterLeft,this.gutterTop + this.halfTextHeight + this.graphheight + 2,RGraph.number_format(this, this.Get('chart.xmin'), units_pre, units_post),'center','center');}
}
this.context.fill();this.context.stroke();}
if(typeof(this.Get('chart.labels')) == 'object'){
var xOffset = 5;var font = this.Get('chart.text.font');this.context.fillStyle = this.Get('chart.text.color');var barHeight = (RGraph.GetHeight(this) - this.gutterTop - this.gutterBottom ) / this.Get('chart.labels').length;yTickGap = (RGraph.GetHeight(this) - this.gutterTop - this.gutterBottom) / this.Get('chart.labels').length
var i=0;for (y=this.gutterTop + (yTickGap / 2); y<=RGraph.GetHeight(this) - this.gutterBottom; y+=yTickGap){
RGraph.Text(this.context, font,this.Get('chart.text.size'),this.gutterLeft - xOffset,y,String(this.Get('chart.labels')[i++]),'center','right');}
}
}
RGraph.HBar.prototype.Drawbars = function ()
{
this.context.lineWidth = 1;this.context.strokeStyle = this.Get('chart.strokestyle');this.context.fillStyle = this.Get('chart.colors')[0];var prevX = 0;var prevY = 0;if(this.Get('chart.xmax')){
this.scale = [
(((this.Get('chart.xmax') - this.Get('chart.xmin')) * 0.2) + this.Get('chart.xmin')).toFixed(this.Get('chart.scale.decimals')),
(((this.Get('chart.xmax') - this.Get('chart.xmin')) * 0.4) + this.Get('chart.xmin')).toFixed(this.Get('chart.scale.decimals')),
(((this.Get('chart.xmax') - this.Get('chart.xmin')) * 0.6) + this.Get('chart.xmin')).toFixed(this.Get('chart.scale.decimals')),
(((this.Get('chart.xmax') - this.Get('chart.xmin')) * 0.8) + this.Get('chart.xmin')).toFixed(this.Get('chart.scale.decimals')),
(((this.Get('chart.xmax') - this.Get('chart.xmin')) + this.Get('chart.xmin'))).toFixed(this.Get('chart.scale.decimals'))
];this.max = this.scale[4];} else {
var grouping = this.Get('chart.grouping');for (i=0; i<this.data.length; ++i){
if(typeof(this.data[i]) == 'object'){
var value = grouping == 'grouped' ? Number(RGraph.array_max(this.data[i], true)) : Number(RGraph.array_sum(this.data[i])) ;} else {
var value = Number(Math.abs(this.data[i]));}
this.max = Math.max(Math.abs(this.max), Math.abs(value));}
this.scale = RGraph.getScale(this.max, this);if(this.Get('chart.xmin') > 0){
this.scale[0] = Number((((this.scale[4] - this.Get('chart.xmin')) * 0.2) + this.Get('chart.xmin')).toFixed(this.Get('chart.scale.decimals')));this.scale[1] = Number((((this.scale[4] - this.Get('chart.xmin')) * 0.4) + this.Get('chart.xmin')).toFixed(this.Get('chart.scale.decimals')));this.scale[2] = Number((((this.scale[4] - this.Get('chart.xmin')) * 0.6) + this.Get('chart.xmin')).toFixed(this.Get('chart.scale.decimals')));this.scale[3] = Number((((this.scale[4] - this.Get('chart.xmin')) * 0.8) + this.Get('chart.xmin')).toFixed(this.Get('chart.scale.decimals')));this.scale[4] = Number((((this.scale[4] - this.Get('chart.xmin')) * 1.0) + this.Get('chart.xmin')).toFixed(this.Get('chart.scale.decimals')));}
this.max = this.scale[4];}
if(this.Get('chart.scale.decimals') == null && Number(this.max) == 1){
this.Set('chart.scale.decimals', 1);}
var colorIdx = 0;var graphwidth = (this.canvas.width - this.gutterLeft - this.gutterRight);var halfwidth = graphwidth / 2;for (i=0; i<this.data.length; ++i){
var width = (this.data[i] / this.max) *  graphwidth;var height = this.graphheight / this.data.length;var orig_height = height;var x = this.gutterLeft;var y = this.gutterTop + (i * height);var vmargin = this.Get('chart.vmargin');if(width < 0){
x -= width;width = Math.abs(width);}
if(this.Get('chart.shadow')){
this.context.shadowColor = this.Get('chart.shadow.color');this.context.shadowBlur = this.Get('chart.shadow.blur');this.context.shadowOffsetX = this.Get('chart.shadow.offsetx');this.context.shadowOffsetY = this.Get('chart.shadow.offsety');}
this.context.beginPath();if(typeof(this.data[i]) == 'number'){
var barHeight = height - (2 * vmargin);var barWidth = ((this.data[i] - this.Get('chart.xmin')) / (this.max - this.Get('chart.xmin'))) * this.graphwidth;var barX = this.gutterLeft;if(this.Get('chart.yaxispos') == 'center'){
barWidth /= 2;barX += halfwidth;if(this.data[i] < 0){
barWidth = (Math.abs(this.data[i]) - this.Get('chart.xmin')) / (this.max - this.Get('chart.xmin'));barWidth = barWidth * (this.graphwidth / 2);barX = ((this.graphwidth / 2) + this.gutterLeft) - barWidth;}
}
this.context.strokeStyle = this.Get('chart.strokestyle');this.context.fillStyle = this.Get('chart.colors')[0];if(this.Get('chart.colors.sequential')){
this.context.fillStyle = this.Get('chart.colors')[colorIdx++];}
this.context.strokeRect(barX, this.gutterTop + (i * height) + this.Get('chart.vmargin'), barWidth, barHeight);this.context.fillRect(barX, this.gutterTop + (i * height) + this.Get('chart.vmargin'), barWidth, barHeight);this.coords.push([barX,
y + vmargin,
barWidth,
height - (2 * vmargin),
this.context.fillStyle,
this.data[i],
true]);} else if(typeof(this.data[i]) == 'object' && this.Get('chart.grouping') == 'stacked'){
if(this.Get('chart.yaxispos') == 'center'){
alert('[HBAR] You can\'t have a stacked chart with the Y axis in the center, change it to grouped');}
var barHeight = height - (2 * vmargin);for (j=0; j<this.data[i].length; ++j){
this.context.strokeStyle = this.Get('chart.strokestyle');this.context.fillStyle = this.Get('chart.colors')[j];if(this.Get('chart.colors.sequential')){
this.context.fillStyle = this.Get('chart.colors')[colorIdx++];}
var width = (((this.data[i][j]) / (this.max))) * this.graphwidth;var totalWidth = (RGraph.array_sum(this.data[i]) / this.max) * this.graphwidth;this.context.strokeRect(x, this.gutterTop + this.Get('chart.vmargin') + (this.graphheight / this.data.length) * i, width, height - (2 * vmargin) );this.context.fillRect(x, this.gutterTop + this.Get('chart.vmargin') + (this.graphheight / this.data.length) * i, width, height - (2 * vmargin) );this.coords.push([x,
y + vmargin,
width,
height - (2 * vmargin),
this.context.fillStyle,
RGraph.array_sum(this.data[i]),
j == (this.data[i].length - 1)
]);x += width;}
} else if(typeof(this.data[i]) == 'object' && this.Get('chart.grouping') == 'grouped'){
for (j=0; j<this.data[i].length; ++j){
if(this.Get('chart.shadow')){
RGraph.SetShadow(this, this.Get('chart.shadow.color'), this.Get('chart.shadow.offsetx'), this.Get('chart.shadow.offsety'), this.Get('chart.shadow.blur'));}
this.context.strokeStyle = this.Get('chart.strokestyle');this.context.fillStyle = this.Get('chart.colors')[j];if(this.Get('chart.colors.sequential')){
this.context.fillStyle = this.Get('chart.colors')[colorIdx++];}
var width = ((this.data[i][j] - this.Get('chart.xmin')) / (this.max - this.Get('chart.xmin'))) * (RGraph.GetWidth(this) - this.gutterLeft - this.gutterRight );var individualBarHeight = (height - (2 * vmargin)) / this.data[i].length;var startX = this.gutterLeft;var startY = y + vmargin + (j * individualBarHeight);if(this.Get('chart.yaxispos') == 'center'){
width  /= 2;startX += halfwidth;}
if(width < 0){
startX += width;width *= -1;}
this.context.strokeRect(startX, startY, width, individualBarHeight);this.context.fillRect(startX, startY, width, individualBarHeight);this.coords.push([startX,
startY,
width,
individualBarHeight,
this.context.fillStyle,
this.data[i][j],
true]);}
}
this.context.closePath();}
this.context.fill();this.context.stroke();RGraph.NoShadow(this);this.RedrawBars();}
RGraph.HBar.prototype.RedrawBars = function ()
{
if(this.Get('chart.noredraw')){
return;}
var coords = this.coords;var font = this.Get('chart.text.font');var size = this.Get('chart.text.size');var color = this.Get('chart.text.color');RGraph.NoShadow(this);this.context.strokeStyle = this.Get('chart.strokestyle');for (var i=0; i<coords.length; ++i){
if(this.Get('chart.shadow')){
this.context.beginPath();this.context.strokeStyle = this.Get('chart.strokestyle');this.context.fillStyle = coords[i][4];this.context.lineWidth = 1;this.context.strokeRect(coords[i][0], coords[i][1], coords[i][2], coords[i][3]);this.context.fillRect(coords[i][0], coords[i][1], coords[i][2], coords[i][3]);this.context.fill();this.context.stroke();}
if(this.Get('chart.labels.above') && coords[i][6]){
this.context.fillStyle = color;this.context.strokeStyle = 'black';RGraph.NoShadow(this);var border = (coords[i][0] + coords[i][2] + 7 + this.context.measureText(this.Get('chart.units.pre') + this.coords[i][5] + this.Get('chart.units.post')).width) > RGraph.GetWidth(this) ? true : false;RGraph.Text(this.context,
font,
size,
coords[i][0] + coords[i][2] + (border ? -5 : 5),
coords[i][1] + (coords[i][3] / 2),
RGraph.number_format(this, (this.coords[i][5]).toFixed(this.Get('chart.labels.above.decimals')), this.Get('chart.units.pre'), this.Get('chart.units.post')),
'center',
border ? 'right' : 'left',
border,
null,
border ? 'rgba(255,255,255,0.9)' : null);}
}
}
RGraph.HBar.prototype.getBar = function (e)
{
var canvas = this.canvas;var context = this.context;var mouseCoords = RGraph.getMouseXY(e);for (var i=0,len=this.coords.length; i<len; i++){
var mouseX = mouseCoords[0];var mouseY = mouseCoords[1];var left = this.coords[i][0];var top = this.coords[i][1];var width = this.coords[i][2];var height = this.coords[i][3];var idx = i;if(mouseX >= left && mouseX <= (left + width) && mouseY >= top && mouseY <= (top + height) ){
return {
0: this,   'object': this,
1: left,   'x': left,
2: top,    'y': top,
3: width,  'width': width,
4: height, 'height': height,
5: idx,    'index': idx
};}
}
}
RGraph.HBar.prototype.getValue = function (arg)
{
if(arg.length == 2){
var mouseX = arg[0];var mouseY = arg[1];} else {
var mouseCoords = RGraph.getMouseXY(arg);var mouseX = mouseCoords[0];var mouseY = mouseCoords[1];}
if(   mouseY < this.Get('chart.gutter.top')
|| mouseY > (this.canvas.height - this.Get('chart.gutter.bottom'))
|| mouseX < this.Get('chart.gutter.left')
|| mouseX > (this.canvas.width - this.Get('chart.gutter.right'))
){
return null;}
if(this.Get('chart.yaxispos') == 'center'){
var value = ((mouseX - this.Get('chart.gutter.left')) / (this.graphwidth / 2)) * (this.max - this.Get('chart.xmin'));value = value - this.max
if(this.Get('chart.xmin') > 0){
value = ((mouseX - this.Get('chart.gutter.left') - (this.graphwidth / 2)) / (this.graphwidth / 2)) * (this.max - this.Get('chart.xmin'));value += this.Get('chart.xmin');if(mouseX < (this.gutterLeft + (this.graphwidth / 2))){
value -= (2 * this.Get('chart.xmin'));}
}
} else {
var value = ((mouseX - this.Get('chart.gutter.left')) / this.graphwidth) * (this.max - this.Get('chart.xmin'));value += this.Get('chart.xmin');}
return value;}
RGraph.HBar.prototype.Highlight = function (shape)
{
RGraph.Highlight.Rect(this, shape);}