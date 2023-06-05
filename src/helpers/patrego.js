$('document').ready(function(){
	loadGoods();
});

function loadGoods() {
	$.getJSON('src/goods/goods.json', function (data) {
		//console.log(data);
		var out = '';
		for (var key in data) {
			out+='<p>'+data[key]['name']+'</p>';
		}
	})
}