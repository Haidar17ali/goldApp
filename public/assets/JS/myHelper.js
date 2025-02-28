function kubikasi(diameter,length,qty){
    return parseFloat(diameter*diameter*length*0.7854/1000000*qty).toFixed(4);
}

function loadWithData(url, data){
    var results;
    $.ajax({
        url: url,
        async: false,
        data: data,
        dataType: "json",
        success: function(datas){
            results = datas;
        }
    });
    return results;
}