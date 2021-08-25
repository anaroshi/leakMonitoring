'use strict';
$(document).ready(function () {


  $('.hPname').on('click', function () {

    let _td = $(this).closest('tr').children();
    let tNo = _td.eq(0).text();
    //  let sid = _td.eq(1).text();
    let intervalMinute = _td.find('input').val();

    let sid = 'daeguf';

    //alert(`tNo : ${tNo}, intervalMinute : ${intervalMinute}`);

    showLoadingBar();

    // **************  프로젝트 추가작업은 여기서   
    processSub(sid, '0102_gamyeongpark', intervalMinute, '9');
    processSub(sid, '0108_dsmc', intervalMinute, '10');
    processSub(sid, '0109_dsmc', intervalMinute, '11');
    processSub(sid, '0114_hyanggyo', intervalMinute, '12');
    processSub(sid, '0301_bisandye', intervalMinute, '13');
    processSub(sid, '0307_daepyong_ms', intervalMinute, '14');
    processSub(sid, '0308_bloodinfo', intervalMinute, '15');
    processSub(sid, '0320_ksschool_ms', intervalMinute, '16');
    processSub('gapyeong', 'gapyeong', intervalMinute, '35');
    processSub('gochang', 'gochang_namgu', intervalMinute, '39');
    processSub('goesan', 'goesan', intervalMinute, '40');
    hideLoadingBar();

  });

  /************* [HEAD][TR] Show & Hide **************/
  $('.hOnoff').on('click', function () {

    if ($('.sTrEach').css('display') == 'none') {
      $('.sTrEach').show();
      $(this).text('Off');
      $('.tDetail').css('visibility', 'visible');
    } else {

      $('.sTrEach').hide();
      $(this).text('On');
      $('.tDetail').css('visibility', 'hidden');
    }
  });

  /************* [HEAD][TD] Show & Hide  **************/
  $('.hDetail').on('click', function () {

    if ($('.sInfo').css('display') == 'none') {
      $('.sInfo').show();
      $(this).text('∧');
      $(this).css('background-color', '#feb546');
      $(this).css('color', '#4d4d4d');
    } else {
      $('.sInfo').hide()
      $(this).text('∨');
      $(this).css('background-color', '#feb546');
      $(this).css('color', '#4d4d4d');
    }
  });

  $('.tComment').on('focusout', function (event) {

    let _td = $(this).closest('tr').children();
    let tNo = _td.eq(0).text();
    let sid = _td.eq(1).text();
    let pname = _td.eq(2).text();
    let tComment = $('.tComment_' + tNo).val();

    //alert(`tNo : ${tNo}, sid : ${sid}, pname : ${pname}, tComment : ${tComment}`);
    showLoadingBar();

    if (pname != '') {
      $.ajax({
        url: 'project_comment.php',
        method: 'POST',
        data: { sid, pname, tComment },
        dataType: 'html',
      })
        .done(async function (data) {
          console.log('success, project comment');
          hideLoadingBar();
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
          console.log('서버오류: ' + textStatus);
          console.log('서버오류: ' + errorThrown);
        })
    }
  });

  
  /**
  * Project_report page
  */
  $('.tNo').on('click', function () {

    let _td = $(this).closest('tr').children();
    let sid = _td.eq(1).text();
    let pname = _td.eq(2).text();
    //alert(`Process pname : ${pname}, sid : ${sid}`);

    location.href = "./project_report.php?sid=" + sid + "&pname=" + pname;

  });

  /**
  * 센서별 접속 시간 확인 페이지
  */
   $(document).on('click', 'div.sSn', function () {
    let sn      = $(this).text();    
    let _td     = $(this).closest('tbody').find('tr').children();
    let sid     = _td.eq(1).text();
    let pname   = _td.eq(2).text();    
    alert (sid+":"+pname+":"+sn);

    location.href = "../sensorRpInterval/src/sensorRpInterval.php?sid=" + sid + "&pname=" + pname+ "&sn=" + sn;

  });

  $(document).on('mouseover', 'div.sSn', function () {
    let _tr = "";
    _tr = $(this).closest('tr');
    $('.sTrEach').css('border','0px');
    _tr.css('border-top', '1px solid red');
    _tr.css('border-bottom', '1px solid red');
  });

  // $(document).on('mouseout', 'div.sSn', function () {  
  //   let _tr = "";
  //   _tr = $(this).closest('tr');
  //   _tr.css('border','0px');
  // });
});


// 센서 list & info

function process(sid, pname, selector) {
  let intervalMinute = $('.tMinute_' + selector).val();
  processSub(sid, pname, intervalMinute, selector);
}

function processSub(sid, pname, intervalMinute, selector) {
  //  alert(`Process pname : ${pname}, sid : ${sid}, intervalMinute : ${intervalMinute}, selector : ${selector}`);

  let iMinute = `-${intervalMinute} minutes`;
  let _tr = $('.tPname_' + selector).closest('tr');

  showLoadingBar();

  if (pname != '') {

    $.ajax({
      url: 'project_list_info.php',
      method: 'POST',
      data: { sid, pname, iMinute, intervalMinute, selector },
      dataType: 'json',
    })
    .done(async function (data) {
      hideLoadingBar();
      _tr.css('background-color', 'orange');
      _tr.find('.tTotal').html(data.lastNum);
      _tr.find('.tSum').html(data.completeNum);
      _tr.find('.tNow').html(data.tNow);
      _tr.next().find('.tdLast').html(data.output);
      _tr.find('input[name=tMinute]').val(data.intervalMinute);
      if (data.total > 0) {
        _tr.find('.tOnoff').text('Off');
        _tr.find('.tDetail').css('visibility', 'visible');
        _tr.find('.tDetail_off').text('∧');
      } else {
        _tr.find('.tOnoff').text('None');
        _tr.find('.tDetail').css('visibility', 'hidden');
      }

      // default
      $('.sInfo').hide()
      $('.hDetail').text('∨');
      $('.hDetail').css('background-color', '#feb546');
      $('.hDetail').css('color', '#4d4d4d');

    })
    .fail(function (jqXHR, textStatus, errorThrown) {
      console.log('서버오류: ' + textStatus);
      console.log('서버오류: ' + errorThrown);
    });
  }
}

// 센서내역 접기
function onoff(selector) {
  let onoff = '.tOnoff_' + selector;
  let tdLast = '.tdLast_' + selector;
  let tDetail = '.tDetail_' + selector;
  let text = $(onoff).text();

  if ($(tdLast).css('display') == 'none') {
    $(tdLast).show();
    if (text !== 'None') $(onoff).text('Off');
    $(tDetail).css('visibility', 'visible');
  } else {
    $(tdLast).hide();
    if (text !== 'None') $(onoff).text('On');
    $(tDetail).css('visibility', 'hidden');
  }
}

// 센서의 상세내역 접기
function detail(selector) {

  let sInfo = '.sInfo_' + selector;
  let tDetail_off = '.tDetail_off_' + selector;

  if ($(sInfo).css('display') == 'none') {

    $(sInfo).show();
    $(tDetail_off).text('∧');

  } else {

    $(sInfo).hide();
    $(tDetail_off).text('∨');
  }
}

/************* LoadingBar **************/
function showLoadingBar() {
  let maskHeight = $(document).height();
  let maskWidth = window.document.body.clientWidth;
  let mask = "<div id='mask' style='position:absolute; z-index:9000; background-color:#000000; display:none; left:0; top:0;'></div>";
  let loadingImg = "";
  loadingImg += "<div id='loadingImg' style='position:absolute; left:30%; top:40%; display:none; z-index:10000;'>";
  loadingImg += " <img src='../image/spinner.gif'/>";
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

function sComm(sid, pname, sn, sComment) {

  //alert(`sid : ${sid}, pname : ${pname}, sn : ${sn}, sComment : ${sComment}`);

  if (pname != '') {
    $.ajax({
      url: 'sn_comment.php',
      method: 'POST',
      data: { sid, pname, sn, sComment },
      dataType: 'html',
    })
      .done(async function (data) {
        console.log('success, sensor comment');
      })
      .fail(function (jqXHR, textStatus, errorThrown) {
        console.log('서버오류: ' + textStatus);
        console.log('서버오류: ' + errorThrown);
      })
  }

}
