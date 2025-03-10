function kubikasi(diameter,length,qty){
    return parseFloat(diameter*diameter*length*0.7854/1000000*qty).toFixed(4);
}

function totalKubikasi(details){
    let total = 0;
    if (details.length > 0) {
        details.forEach(detail => {
            total += kubikasi(detail.diameter, detail.length, detail.qty); 
        });
    }
    return total;
}

function nominalKubikasi(details){
    let total = 0;
    if (details.length > 0) {
        details.forEach(detail => {
            total += kubikasi(detail.diameter, detail.length, detail.qty) * detail.price; 
        });
    }
    return money_format(total,0, ',', '.');
}

function loadWithData(url, data){

    if ($('#loading').length) {  
        $('#loading').fadeIn(); // Munculkan efek loading
    }

    var results;
    $.ajax({
        url: url,
        async: false,
        data: data,
        dataType: "json",
        success: function(datas){            
            results = datas;
        },
        complete: function() {
            $('#loading').hide(); // Sembunyikan loading setelah request selesai
        }
    });
    return results;
}

function money_format(number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function(n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };

    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}