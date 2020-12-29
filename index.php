<html>
<head>
  <meta charset='utf-8'/>
  <meta name='viewport' content='initial-scale=1'/>
  <meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'/>

  <title>TOC B-Ball Stat Tracker</title>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script> <!-- Thank you Google! -->
  <link rel='stylesheet' type='text/css' href='style.css'/>
  <script type='text/javascript'>
    function toggleVisibility(divId) {
      el = document.getElementById(divId);
      el.hidden = (el.hidden == true) ? false : true;
    }

    function showHideDiv(showDivId, hideDivArray) {
      document.getElementById(showDivId).hidden = false;
      for (var i = 0; i < hideDivArray.length; i++) {
        document.getElementById(hideDivArray[i]).hidden = true;
      }
    }

    function populateDropdown(firstOption, dropdownID, optionList) {
      $("#" + dropdownID).append(new Option(firstOption[0], firstOption[1]));

      $.each(optionList, function (index, value) {
        //console.log( index + ": " + value );
        $("#" + dropdownID).append(new Option(value, value));
      });
    }

    function fetchUsers() {
      var users = [];
      $.ajax({
        async: false, type: "GET", // We want to get data from server (no modifications, etc)
        url: 'getUsers.php', data: '&ts=' + $.now(), success: function (data) {
          data = data.split(',');
          data.push("New Player");
          // alert(data); // show response from the php script.
          users = data;
        }
      });
      return users;
    }

    function clearForm() {
      $("#scorerSelect,#shooterSelect").each(function () {
        if ($(this).is("select")) {
          $(this).empty();
        } else {
          $(this).replaceWith("<select id='" + $(this).attr('id') + "' name='" + $(this).attr('id') + "'></select>");
        }
      });
      $("#round1Select").empty();
      $("#round2Select").empty();
      $("#round3Select").empty();
      $("#totalScore").val(0);
    }

    function populateForm() {
      users = fetchUsers();
      console.log(users);
      populateDropdown(['Select Shooter', ''], "shooterSelect", users);
      populateDropdown(['Select Scorer', ''], "scorerSelect", users);
      populateDropdown(['--', ''], "round1Select", ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10']);
      populateDropdown(['--', ''], "round2Select", ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10']);
      populateDropdown(['--', ''], "round3Select", ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10']);
      $("#scorerSelect").attr("disabled", true);
      $("#shooterSelect").change(function () {
        $("#scorerSelect").attr("disabled", false);
        if ($("#shooterSelect").val() != 'New Player') {
          $("#scorerSelect option[value='" + $("#shooterSelect").val() + "']").attr("disabled", true);
        }
      });
      $("#scorerSelect,#shooterSelect").each(function () {
        $(this).change(function () {
          if ($(this).val() == 'New Player') {
            $(this).replaceWith(
                '<input type="text" size="10" id="' + $(this).attr('id') + '" name="' + $(this).attr('id') + '"/>');
          } else if ($(this).val() != '') {
            $(this).removeClass("errorInput");
          }
        });
      });
      $("#round1Select,#round2Select,#round3Select").each(function () {
        $(this).change(function () {
          $("#totalScore").val(parseInt($("#round1Select").val()) + parseInt($("#round2Select").val()) + parseInt(
              $("#round3Select").val()));
          if ($(this).val() != '') {
            $(this).removeClass("errorInput");
          }
        });
      });
    }

    function fetchStats() {
      var stats = [];
      $.ajax({
        async: false, type: "GET", // We want to get data from server (no modifications, etc)
        url: 'getStats.php', data: '&ts=' + $.now(), success: function (data) {
          data = data.split(',');
          stats = data;
        }
      });
      return stats;
    }

    function parseBasicStats(stats) {
      console.log(stats);
      var parts = stats.split('|');
      return "<b>" + parts[0] + "</b> has <b>" + parts[1] + "</b> / <b>" + parts[2] + "</b> baskets for <b>"
          + Math.floor((parts[1] / parts[2]) * 100) + "%</b>" + '\n';
    }

    function showStats() {
      // alert("Refreshing Stats!");
      $("#returnedStats").text("");
      stats = fetchStats();
      var rtnStr = "";
      $(stats).each(function () {
        rtnStr = parseBasicStats(this);
        $("#returnedStats").append("<span>" + rtnStr + "</span>");
        $("#returnedStats").append("<br/>");
      });
    }

    $(document).ready(function () {
      $("#refreshStats").click(function () {
        showStats();
      });

      populateForm();

      // Form Submittion Process
      var request;
      $("#scoreForm").submit(function (event) {
        // abort any pending request
        if (request) {
          request.abort();
        }

        var errorFlag = false;
        $("#scorerSelect,#shooterSelect,#round1Select,#round2Select,#round3Select").each(function () {
          if ($(this).val() == "") {
            $(this).addClass("errorInput");
            errorFlag = true;
          }
        });

        if (errorFlag) {
          $("#submit").removeAttr('disabled');
          return false;
        } else {
          // alert("Looks good, submit!!");
          var $inputs = $(this).find("input, select");
          // let's disable the inputs for the duration of the ajax request
          $inputs.prop("disabled", true);
          $("#submit").attr('disabled', 'disabled');

          var formSerialData = $("#scoreForm").serialize();

          request = $.ajax({
            type: "POST", url: "submitScores.php", 			// the script where you handle the form input.
            data: {
              shooterSelect: $("#shooterSelect").val(),
              scorerSelect: $("#scorerSelect").val(),
              round1Select: $("#round1Select").val(),
              round2Select: $("#round2Select").val(),
              round3Select: $("#round3Select").val()
            },//$(this).parents('form').serialize(), 	// serializes the form's elements.
            beforeSend: function (jqXHR, settings) {
              $("#shooterSelect").val($("#shooterSelect").val().replace(/ /g, ''));
              $("#scorerSelect").val($("#scorerSelect").val().replace(/ /g, ''));
              settings.data += "&ts=" + $.now();
              console.log(settings.data);
              // return false;
            }, success: function (data) {
              alert(data); // show response from the php script.
              // Reset Form
              clearForm();
              // Repopulate Form
              populateForm();
            }, complete: function () { // callback handler that will be called regardless if the request failed or succeeded
              // reenable the inputs
              $inputs.prop("disabled", false);
              $("#totalScore").prop("disabled", true);
            }
          });

          // prevent default posting of form
          event.preventDefault();
          return false; // avoid to execute the actual submit of the form.
        }
      });
    });
  </script>
</head>

<body>
<div id='wrapper'>
  <header></header>
  <div id='main'>
    <div id='menu' align='center'>
      <div class='menuItem' id='statslink' onclick='showHideDiv("stats",["enterscore"]);showStats();'>View Stats</div>
      <div class='menuItem' id='enterscorelink' onclick='showHideDiv("enterscore",["stats"])'>Enter Score</div>
    </div>
    <div id='content' align='center'>
      <div id='stats' hidden>
        Here, You will be able to view some stats
        <hr/>
        <button id="refreshStats" type="button">Refresh Stats</button>
        <hr/>
        <div id="returnedStats"></div>
      </div>
      <div id='enterscore' hidden>
        Here, you will enter scores<br/>
        <form id='scoreForm'>
          Shooter: <select id='shooterSelect' name='shooterSelect'></select><br/>
          Scorer: <select id='scorerSelect' name='scorerSelect'></select><br/>
          Round 1: <select id='round1Select' name='round1Select'></select>/10<br/>
          Round 2: <select id='round2Select' name='round2Select'></select>/10<br/>
          Round 3: <select id='round3Select' name='round3Select'></select>/10
          <hr width="150px"/>
          Total:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input id='totalScore' name='totalScore' value='0' maxlength="2"
                                                           size="2" disabled></select>/30<br/>
          <input type='submit' id='submit' value='Submit Score'/>
        </form>
      </div>
      <div id='statslink'></div>
      <div id='statslink'></div>
    </div>
  </div>
</div>
</body>
</html>
