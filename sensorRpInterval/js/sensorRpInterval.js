$(document).ready(function () {
  
  // 초기화 Button
  $('.sensor_clear').on('click', function () {
    location.reload();
  });

  // 조회 Button
  $('.sensor_search').on('click', function (e) {
    e.preventDefault();
    let sid         = $('.sensor_sid').val();
    let project     = $('.sensor_pname').val();
    let sn          = $('.sensor_sn').val();
    let srhDateFrom = $('.srhDateFrom').val();
    let srhTimeFrom = $('.srhTimeFrom').val();    
    let srhDateTo   = $('.srhDateTo').val();
    let srhTimeTo   = $('.srhTimeTo').val();
    
    let date        = new Date();
    let year        = date.getFullYear();
    let month       = date.getMonth()+1;
    let day         = date.getDate();

    if((month+"").length<2) {
      month = "0" + month;
    }
    if((day+"").length<2) {
      day = "0" + day;
    }

    let getToday = year+"-"+month+"-"+day;
    
    //alert("sid:"+sid+"project:"+project+" / sn:"+sn+" / from:"+srhDateFrom+" "+srhTimeFrom+"~"+srhDateTo+" "+srhTimeTo+" / today:"+getToday);

    if (srhDateFrom > getToday) {
      alert("조회 시작일자가 오늘보다 미래입니다.")
      $('.srhDateFrom').focus();
      return false;
    }

    if (srhDateTo != "") {
      if(srhDateTo > getToday) {
        srhDateTo = getToday;
      }
    }

    if (srhDateFrom != "" && srhDateTo == "") {
        srhDateTo = getToday;
        srhTimeTo = "23:59:59";
    }

    if (srhDateTo<srhDateFrom) {
      alert("조회 시작일자가 끝일자보다 큽니다.");
      $('.srhDateTo').focus();
      return false;
    }
    
    if (srhDateFrom=="") srhTimeFrom = "";
    if (srhTimeFrom=="") srhTimeFrom = "00:00:00";
    if (srhDateTo=="") srhTimeTo = "";
    if (srhTimeTo=="") srhTimeTo = "23:59:59";
    
    showLoadingBar();

    //alert("sid:"+sid+"project:"+project+" / sn:"+sn+" / from:"+srhDateFrom+" "+srhTimeFrom+"~"+srhDateTo+" "+srhTimeTo+" / today:"+getToday);
    $.ajax({
      type: "POST",
      url: "./sensorRpInterval_list.php",
      data: { sid, project, sn, srhDateFrom, srhTimeFrom, srhDateTo, srhTimeTo },
      dataType: "json",
    })
    .done(async function (data) {
      console.log('success');      
      $('.sensor_head_sumTimediffValeu').html(data.total);
      $('.tb_sensor').html(data.outputList);
      hideLoadingBar();
    })
    .fail(function (jqXHR, textStatus, errorThrown) {
      console.log('서버오류: ' + textStatus);
      console.log('서버오류: ' + errorThrown);
    });
  });


  // 뒤로 Button
   $('.backPage').on('click', function () {
    window.history.back();
  });

});

/************* LoadingBar **************/
function showLoadingBar() {
  let maskHeight = $(document).height();
  let maskWidth = window.document.body.clientWidth;
  let mask = "<div id='mask' style='position:absolute; z-index:9000; background-color:#000000; display:none; left:0; top:0;'></div>";
  let loadingImg = "";
  loadingImg += "<div id='loadingImg' style='position:absolute; left:30%; top:40%; display:none; z-index:10000;'>";
  loadingImg += " <img src='../../../image/spinner.gif'/>";
  loadingImg += "</div>";
  $('body').append(mask).append(loadingImg);
  $('#mask').css({ 'width': maskWidth, 'height': maskHeight, 'opacity': '0.1' });
  $('#mask').show();
  $('#loadingImg').show();
}

function hideLoadingBar() {
  $('#mask, #loadingImg').hide();
  $('#mask, #loadingImg').remove();
}