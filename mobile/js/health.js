function initHealth() {
  var rightPanel = '<ul data-role="listview" class="ui-icon-alt">';
  rightPanel += '<li><a id="bt_refreshCron" href="#"><i class="fas fa-refresh"></i> {{Rafraichir}}</a></li>';
    rightPanel += '<li><a class="ui-bottom-sheet-link ui-btn ui-btn-inline waves-effect waves-button" href="index.php?v=d"><i class="fas fa-desktop"></i> {{Version desktop}}</a></li>';
  rightPanel += '<li><a class="link ui-bottom-sheet-link ui-btn ui-btn-inline waves-effect waves-button" data-page="deamon" data-title="{{Démons}}"><i class="fas fa-bug" ></i> {{Démons}}</a></li>';
  rightPanel += '<li><a class="link ui-bottom-sheet-link ui-btn ui-btn-inline waves-effect waves-button" data-page="cron" data-title="{{Crons}}"><i class="fas fa-cogs" ></i> {{Crons}}</a></li>';
  rightPanel += '<li><a class="link ui-bottom-sheet-link ui-btn ui-btn-inline waves-effect waves-button" data-page="alert" data-title="{{Alertes}}"><i class="icon nextdom-alerte" ></i> {{Alertes}}</a></li>';
  rightPanel += '</ul>';
  panel(rightPanel);
  getHealth();

  $('#bt_refreshCron').on('click',function(){
    getHealth();
  });

  function getHealth(){
    $('#table_health tbody').empty();
    nextdom.health({
      error: function (error) {
        notify("Erreur", error.message, 'error');
      },
      success: function (data) {
        var html = '';
        for(var i in data){
         html += '<tr>';
         html += '<td>';
         html += data[i].name;
         html += '</td>';
          html += '<td>';
         if(data[i].state){
          html += '<td style="background-color:#3ADF00">';
        }else{
          html += '<td style="background-color:#FF0000">';
        }
        html += data[i].result;
         html += '<a>';
        html += '</td>';
        html += '<td>';
        html += data[i].comment;
        html += '</td>';
        html += '</tr>';
      }
      $('#table_health tbody').append(html);
    }
  });
  }


}