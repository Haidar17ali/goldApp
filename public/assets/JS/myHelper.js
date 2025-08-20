function kubikasi(diameter,length,qty){
    return parseFloat(diameter*diameter*length*0.7854/1000000*qty).toFixed(4);
}

function kubikasiCore(lenght,width,height, qty){
    return parseFloat(lenght*width*height*qty/1000).toFixed(4);
}

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

function getDetailLpb(url, data){
    let lpbDetail = loadWithData(url, data);



    $("#modalKitir").text(lpbDetail.no_kitir);
    $("#modalKode").text(lpbDetail.code);
    $("#modalSupplier").text(lpbDetail.supplier != null ? lpbDetail.supplier.name : "");
    $("#modalNopol").text(lpbDetail.nopol);
    $("#modalVehicle").text(lpbDetail.road_permit != null ? lpbDetail.road_permit.vehicle :
        "Kendaraan tidak ditemukan!");

    // table
    // Loop data.details dan masukkan ke dalam tabel
    let tableContent = "";
    let tFootContent = "";
    let totalLPB = 0;
    let totalAfkir = 0;
    let total130 = 0;
    let total260 = 0;
    lpbDetail.details.forEach((detail, index) => {
        totalLPB += kubikasi(detail.diameter, detail.length, detail.qty) * detail.price

        if(detail.quality == 'Afkir' && detail.length == "130"){
            totalAfkir += detail.qty;
        }else if(detail.quality == 'Super' && detail.length == "130"){
            total130 += detail.qty;
        }else if(detail.quality == 'Super' && detail.length == "260"){
            total260 += detail.qty;
        }
        tableContent += `
            <tr>
                <td>${detail.product_code}</td>
                <td>${detail.quality}</td>
                <td>${detail.length}</td>
                <td>${detail.diameter}</td>
                <td>${detail.qty}</td>
                <td>${kubikasi(detail.diameter,detail.length,detail.qty)}</td>
                <td>${money_format(detail.price,0,',','.')}</td>
                <td>${money_format(kubikasi(detail.diameter,detail.length,detail.qty)*detail.price,0,',','.')}</td>
            </tr>
        `;
    });

    tableContent += `
            <tr>
                <td class="text-right">Total Afkir:</td>
                <td>${money_format(totalAfkir,0,',','.')}</td>
                <td class="text-right">Total 130:</td>
                <td>${money_format(total130,0,',','.')}</td>
                <td class="text-right">Total 260:</td>
                <td>${money_format(total260,0,',','.')}</td>
                <td class="text-right">Total LPB:</td>
                <td>${money_format(totalLPB,0,',','.')}</td>
            </tr>
            <tr>
                <td colspan="7" class="text-right">Pot Konversi:</td>
                <td>${money_format(lpbDetail.conversion,0,',','.')}</td>
            </tr>
            <tr>
                <td colspan="7" class="text-right">Nilai:</td>
                <td>${money_format(totalLPB+lpbDetail.conversion,0,',','.')}</td>
            </tr>
            <tr>
            <td colspan="7" class="text-right">PPH 22:</td>
            <td>${money_format((totalLPB+(lpbDetail.conversion))*0.0025,0,',','.')}</td>
            </tr>
            <tr>
                <td colspan="7" class="text-right">Total Yang Ditransfer:</td>
                <td>${money_format((totalLPB+(lpbDetail.conversion))-(totalLPB+(lpbDetail.conversion))*0.0025,0,',','.')}</td>
            </tr>
        `;

    // Masukkan ke dalam <tbody>
    $("#modalDetail").html(tableContent);
    $("#infoDetail").html(tFootContent);
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
            $('#loading').fadeOut(); // Sembunyikan loading setelah request selesai
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