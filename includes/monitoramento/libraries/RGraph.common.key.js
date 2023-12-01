// MINIFIED ON: 2012-03-28

if(typeof(RGraph) == 'undefined') RGraph = {};RGraph.DrawKey = function (obj, key, colors)
{
var canvas = obj.canvas;var context = obj.context;context.lineWidth = 1;context.beginPath();var keypos = obj.Get('chart.key.position');var textsize = obj.Get('chart.text.size');if(typeof(obj.Get('chart.key.vpos')) == 'number'){
obj.Set('chart.key.position.y', obj.Get('chart.key.vpos') * this.Get('chart.gutter.top') );}
var key_non_null = [];var colors_non_null = [];for (var i=0; i<key.length; ++i){
if(key[i] != null){
colors_non_null.push(colors[i]);key_non_null.push(key[i]);}
}
key = key_non_null;colors = colors_non_null;if(keypos && keypos == 'gutter'){
RGraph.DrawKey_gutter(obj, key, colors);} else if(keypos && keypos == 'graph'){
RGraph.DrawKey_graph(obj, key, colors);} else {
alert('[COMMON] (' + obj.id + ') Unknown key position: ' + keypos);}
}
RGraph.DrawKey_graph = function (obj, key, colors)
{
var canvas = obj.canvas;var context = obj.context;var text_size = typeof(obj.Get('chart.key.text.size')) == 'number' ? obj.Get('chart.key.text.size') : obj.Get('chart.text.size');var text_font = obj.Get('chart.text.font');var gutterLeft = obj.Get('chart.gutter.left');var gutterRight = obj.Get('chart.gutter.right');var gutterTop = obj.Get('chart.gutter.top');var gutterBottom = obj.Get('chart.gutter.bottom');var hpos = obj.Get('chart.yaxispos') == 'right' ? gutterLeft + 10 : RGraph.GetWidth(obj) - gutterRight - 10;var vpos = gutterTop + 10;var title = obj.Get('chart.title');var blob_size = text_size;var hmargin = 8;var vmargin = 4;var fillstyle = obj.Get('chart.key.background');var strokestyle = '#333';var height = 0;var width = 0;if(!obj.coords) obj.coords = {};obj.coords.key = [];context.font = text_size + 'pt ' + obj.Get('chart.text.font');for (i=0; i<key.length; ++i){
width = Math.max(width, context.measureText(key[i]).width);}
width += 5;width += blob_size;width += 5;width += 5;width += 5;if(   obj.Get('chart.yaxispos') == 'left'
|| (obj.type == 'pie' && !obj.Get('chart.yaxispos'))
|| (obj.type == 'hbar' && !obj.Get('chart.yaxispos'))
|| (obj.type == 'hbar' && obj.Get('chart.yaxispos') == 'center')
|| (obj.type == 'rscatter' && !obj.Get('chart.yaxispos'))
|| (obj.type == 'radar' && !obj.Get('chart.yaxispos'))
|| (obj.type == 'rose' && !obj.Get('chart.yaxispos'))
|| (obj.type == 'funnel' && !obj.Get('chart.yaxispos'))
|| (obj.type == 'vprogress' && !obj.Get('chart.yaxispos'))
|| (obj.type == 'hprogress' && !obj.Get('chart.yaxispos'))
){
hpos -= width;}
if(typeof(obj.Get('chart.key.halign')) == 'string'){
if(obj.Get('chart.key.halign') == 'left'){
hpos = gutterLeft + 10;} else if(obj.Get('chart.key.halign') == 'right'){
hpos = RGraph.GetWidth(obj) - gutterRight  - width;}
}
if(typeof(obj.Get('chart.key.position.x')) == 'number'){
hpos = obj.Get('chart.key.position.x');}
if(typeof(obj.Get('chart.key.position.y')) == 'number'){
vpos = obj.Get('chart.key.position.y');}
if(obj.Get('chart.key.shadow')){
context.shadowColor = obj.Get('chart.key.shadow.color');context.shadowBlur = obj.Get('chart.key.shadow.blur');context.shadowOffsetX = obj.Get('chart.key.shadow.offsetx');context.shadowOffsetY = obj.Get('chart.key.shadow.offsety');}
context.beginPath();context.fillStyle = obj.Get('chart.key.background');context.strokeStyle = 'black';if(arguments[3] != false){
context.lineWidth = typeof(obj.Get('chart.key.linewidth')) == 'number' ? obj.Get('chart.key.linewidth') : 1;if(obj.Get('chart.key.rounded') == true){
context.beginPath();context.strokeStyle = strokestyle;RGraph.strokedCurvyRect(context, AA(this, hpos), AA(this, vpos), width - 5, 5 + ( (text_size + 5) * RGraph.getKeyLength(key)),4);context.stroke();context.fill();RGraph.NoShadow(obj);} else {
context.strokeRect(AA(this, hpos), AA(this, vpos), width - 5, 5 + ( (text_size + 5) * RGraph.getKeyLength(key)));context.fillRect(AA(this, hpos), AA(this, vpos), width - 5, 5 + ( (text_size + 5) * RGraph.getKeyLength(key)));}
}
RGraph.NoShadow(obj);context.beginPath();if(obj.Get('chart.key.colors')){
colors = obj.Get('chart.key.colors');}
for (var i=key.length - 1; i>=0; i--){
var j = Number(i) + 1;if(obj.Get('chart.key.color.shape') == 'circle'){
context.beginPath();context.strokeStyle = 'rgba(0,0,0,0)';context.fillStyle = colors[i];context.arc(hpos + 5 + (blob_size / 2), vpos + (5 * j) + (text_size * j) - text_size + (blob_size / 2), blob_size / 2, 0, 6.26, 0);context.fill();} else if(obj.Get('chart.key.color.shape') == 'line'){
context.beginPath();context.strokeStyle = colors[i];context.moveTo(hpos + 5, vpos + (5 * j) + (text_size * j) - text_size + (blob_size / 2));context.lineTo(hpos + blob_size + 5, vpos + (5 * j) + (text_size * j) - text_size + (blob_size / 2));context.stroke();} else {
context.fillStyle =  colors[i];context.fillRect(hpos + 5, vpos + (5 * j) + (text_size * j) - text_size, text_size, text_size + 1);}
context.beginPath();context.fillStyle = 'black';RGraph.Text(context,
text_font,
text_size,
hpos + blob_size + 5 + 5,
vpos + (5 * j) + (text_size * j),
key[i]);if(obj.Get('chart.key.interactive')){
var px = hpos + 5;var py = vpos + (5 * j) + (text_size * j) - text_size;var pw = width - 5 - 5 - 5;var ph = text_size;obj.coords.key.push([px, py, pw, ph]);}
}
context.fill();if(obj.Get('chart.key.interactive')){
InteractiveKey_line_mousemove = function (e)
{
var objects = RGraph.ObjectRegistry.getObjectsByCanvasID(e.target.id);for (var i=0; i<objects.length; ++i){
var obj = objects[i];var mouseXY = RGraph.getMouseXY(e);var mouseX = mouseXY[0];var mouseY = mouseXY[1];if(obj.coords.key && obj.coords.key.length){
for (var i=0; i<obj.coords.key.length; ++i){
var px = obj.coords.key[i][0];var py = obj.coords.key[i][1];var pw = obj.coords.key[i][2];var ph = obj.coords.key[i][3];if(mouseX > (px-2) && mouseX < (px + pw + 2) && mouseY > (py - 2) && mouseY < (py + ph + 2) ){
mouse_over_key = true;return;}
mouse_over_key = false;if(typeof(obj.Get('chart.tooltips')) == 'object' && typeof(canvas_onmousemove_func) == 'function'){
canvas_onmousemove_func(e);}
}
}
}
}
InteractiveKey_line_mouseup = function (e)
{
var obj = RGraph.ObjectRegistry.getObjectByXY(e);if(!RGraph.is_null(obj) && obj.type == 'line'){
var mouseXY = RGraph.getMouseXY(e);var mouseX = mouseXY[0];var mouseY = mouseXY[1];RGraph.DrawKey(obj, obj.Get('chart.key'), obj.Get('chart.colors'));for (var i=0; i<obj.coords.key.length; ++i){
var px = obj.coords.key[i][0];var py = obj.coords.key[i][1];var pw = obj.coords.key[i][2];var ph = obj.coords.key[i][3];if( mouseX > (px - 2) && mouseX < (px + pw + 2) && mouseY > (py - 2) && mouseY < (py + ph + 2) ){
RGraph.RedrawCanvas(obj.canvas);var index = obj.coords.key.length - i - 1;obj.context.beginPath();obj.context.fillStyle = 'rgba(255,255,255,0.9)';obj.context.fillRect(obj.Get('chart.gutter.left') + 1,obj.Get('chart.gutter.top'),obj.canvas.width, canvas.height - obj.Get('chart.gutter.bottom') - obj.Get('chart.gutter.top'));obj.context.fill();obj.context.beginPath();if(obj.Get('chart.shadow')){
if(typeof(obj.Get('chart.shadow.color')) == 'string'){
RGraph.SetShadow(obj, obj.Get('chart.shadow.color'), obj.Get('chart.shadow.offsetx'), obj.Get('chart.shadow.offsety'), obj.Get('chart.shadow.blur'));} else {
RGraph.SetShadow(obj, obj.Get('chart.shadow.color')[obj.Get('chart.shadow.color').length - 1 - i], obj.Get('chart.shadow.offsetx'), obj.Get('chart.shadow.offsety'), obj.Get('chart.shadow.blur'));}
}
obj.context.strokeStyle = obj.Get('chart.colors')[index];obj.context.lineWidth = obj.Get('chart.linewidth');if(obj.coords2 &&obj.coords2[index] &&obj.coords2[index].length){
for (var j=0; j<obj.coords2[index].length; ++j){
var x = obj.coords2[index][j][0];var y = obj.coords2[index][j][1];if(j == 0){
obj.context.moveTo(x, y);} else {
obj.context.lineTo(x, y);}
}
}
obj.context.stroke();obj.context.lineWidth = 1;obj.context.beginPath();obj.context.strokeStyle = 'black';obj.context.fillStyle = 'white';RGraph.SetShadow(obj, 'rgba(0,0,0,0.5)', 0,0,10);obj.context.strokeRect(px - 2, py - 2, pw + 4, ph + 4);obj.context.fillRect(px - 2, py - 2, pw + 4, ph + 4);obj.context.stroke();obj.context.fill();RGraph.NoShadow(obj);obj.context.beginPath();obj.context.fillStyle = obj.Get('chart.colors')[index];obj.context.fillRect(px, py, blob_size, blob_size);obj.context.fill();obj.context.beginPath();obj.context.fillStyle = obj.Get('chart.text.color');RGraph.Text(obj.context,
obj.Get('chart.text.font'),
obj.Get('chart.text.size'),
px + 5 + blob_size,
py + ph,
obj.Get('chart.key')[obj.Get('chart.key').length - i - 1]
);context.fill();obj.canvas.style.cursor = 'pointer';e.cancelBubble = true;e.stopPropagation();}
canvas.style.cursor = 'default';}
}
}
InteractiveKey_pie_mousemove = function (e)
{
InteractiveKey_line_mousemove(e);}
InteractiveKey_pie_mouseup = function (e)
{
var objects = RGraph.ObjectRegistry.objects.byCanvasID;for (var i=0; i<objects.length; ++i){
if(objects[i][0] == e.target.id && objects[i][1].type == 'pie'){
var obj = objects[i][1];var mouseXY = RGraph.getMouseXY(e);var mouseX = mouseXY[0];var mouseY = mouseXY[1];for (var i=0; i<obj.coords.key.length; ++i){
var index = obj.coords.key.length - i - 1;var px = obj.coords.key[i][0];var py = obj.coords.key[i][1];var pw = obj.coords.key[i][2];var ph = obj.coords.key[i][3];if(mouseX >= (px - 2) && mouseX <= (px + pw + 2) && mouseY >= (py - 2) && mouseY <= (py + ph + 2)){
RGraph.RedrawCanvas(obj.canvas);obj.context.beginPath();obj.context.fillStyle = 'rgba(255,255,255,0.9)';obj.context.fillRect(0,0,obj.canvas.width,obj.canvas.height);obj.context.fill();var segment = obj.angles[index];obj.context.beginPath();RGraph.SetShadow(obj,'gray',0,0,15);obj.context.fillStyle = obj.Get('chart.colors')[index];obj.context.moveTo(obj.angles[index][2], obj.angles[index][3]);obj.context.arc(obj.angles[index][2], obj.angles[index][3], obj.radius, segment[0], segment[1], false);obj.context.closePath();obj.context.fill();obj.context.lineWidth = 1;obj.context.beginPath();obj.context.strokeStyle = 'black';obj.context.fillStyle = 'white';RGraph.SetShadow(obj, 'rgba(0,0,0,0.5)', 0,0,10);obj.context.strokeRect(px - 2, py - 2, pw + 4, ph + 4);obj.context.fillRect(px - 2, py - 2, pw + 4, ph + 4);obj.context.stroke();obj.context.fill();RGraph.NoShadow(obj);obj.context.beginPath();obj.context.fillStyle = obj.Get('chart.colors')[index];obj.context.fillRect(px, py, blob_size, blob_size);obj.context.fill();obj.context.beginPath();obj.context.fillStyle = obj.Get('chart.text.color');RGraph.Text(obj.context,
obj.Get('chart.text.font'),
obj.Get('chart.text.size'),
px + 5 + blob_size,
py + ph,
obj.Get('chart.key')[obj.Get('chart.key').length - i - 1]
);context.fill();e.stopPropagation();return;}
}
}
}
}
}
}
RGraph.DrawKey_gutter = function (obj, key, colors)
{
var canvas = obj.canvas;var context = obj.context;var text_size = typeof(obj.Get('chart.key.text.size')) == 'number' ? obj.Get('chart.key.text.size') : obj.Get('chart.text.size');var text_font = obj.Get('chart.text.font');var gutterLeft = obj.Get('chart.gutter.left');var gutterRight = obj.Get('chart.gutter.right');var gutterTop = obj.Get('chart.gutter.top');var gutterBottom = obj.Get('chart.gutter.bottom');var hpos = RGraph.GetWidth(obj) / 2;var vpos = (gutterTop / 2) - 5;var title = obj.Get('chart.title');var blob_size = text_size;var hmargin = 8;var vmargin = 4;var fillstyle = obj.Get('chart.key.background');var strokestyle = 'black';var length = 0;context.font = text_size + 'pt ' + text_font;for (i=0; i<key.length; ++i){
length += hmargin;length += blob_size;length += hmargin;length += context.measureText(key[i]).width;}
length += hmargin;if(obj.type == 'pie'){
if(obj.Get('chart.align') == 'left'){
var hpos = obj.radius + gutterLeft;} else if(obj.Get('chart.align') == 'right'){
var hpos = obj.canvas.width - obj.radius - gutterRight;} else {
hpos = canvas.width / 2;}
}
  
hpos -= (length / 2);if(typeof(obj.Get('chart.key.position.x')) == 'number'){
hpos = obj.Get('chart.key.position.x');}
if(typeof(obj.Get('chart.key.position.y')) == 'number'){
vpos = obj.Get('chart.key.position.y');}
if(obj.Get('chart.key.position.gutter.boxed')){
if(obj.Get('chart.key.shadow')){
context.shadowColor = obj.Get('chart.key.shadow.color');context.shadowBlur = obj.Get('chart.key.shadow.blur');context.shadowOffsetX = obj.Get('chart.key.shadow.offsetx');context.shadowOffsetY = obj.Get('chart.key.shadow.offsety');}
context.beginPath();context.fillStyle = fillstyle;context.strokeStyle = strokestyle;if(obj.Get('chart.key.rounded')){
RGraph.strokedCurvyRect(context, hpos, vpos - vmargin, length, text_size + vmargin + vmargin)
} else {
context.strokeRect(hpos, vpos - vmargin, length, text_size + vmargin + vmargin);context.fillRect(hpos, vpos - vmargin, length, text_size + vmargin + vmargin);}
context.stroke();context.fill();RGraph.NoShadow(obj);}
if(obj.Get('chart.key.colors')){
colors = obj.Get('chart.key.colors');}
for (var i=0, pos=hpos; i<key.length; ++i){
pos += hmargin;if(obj.Get('chart.key.color.shape') =='line'){
context.beginPath();context.strokeStyle = colors[i];context.moveTo(pos, vpos + (blob_size / 2));context.lineTo(pos + blob_size, vpos + (blob_size / 2));context.stroke();} else if(obj.Get('chart.key.color.shape') == 'circle'){
context.beginPath();context.fillStyle = colors[i];context.moveTo(pos, vpos + (blob_size / 2));context.arc(pos + (blob_size / 2), vpos + (blob_size / 2), (blob_size / 2), 0, 6.28, 0);context.fill();} else {
context.beginPath();context.fillStyle = colors[i];context.fillRect(pos, vpos, blob_size, blob_size);context.fill();}
pos += blob_size;pos += hmargin;context.beginPath();context.fillStyle = 'black';RGraph.Text(context, text_font, text_size, pos, vpos + text_size - 1, key[i]);context.fill();pos += context.measureText(key[i]).width;}
}
RGraph.getKeyLength = function (key)
{
var len = 0;for (var i=0; i<key.length; ++i){
if(key[i] != null){
++len;}
}
return len;}