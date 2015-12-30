<?php
        include 'check_access.php';
?>
<!DOCTYPE html>
<html>
  <head>
    <meta  content="text/html; charset=UTF-8"  http-equiv="content-type">
    <title>Add Task</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
		$("#country").change(function() {
			switch($("#country").val()) {
				case "Angola" :
					$("#institutionlist").empty();
					$("#institutionlist").append('<option value="FC-Universidade Agostinho Neto">');
					$("#institutionlist").append('<option value="FCA-Universidade Jose Eduardo dos Santos">');
					$("#institutionlist").append('<option value="CNIC-Centro Nacional de Investigaçao Cientifica (Ministerio de Ciencias e Tecnologia)">');
					$("#institutionlist").append('<option value="ISCE-Huila">');
					$("#institutionlist").append('<option value="ISPT-Huila">');
					break;
                                case "Botswana" :
                                        $("#institutionlist").empty();
					$("#institutionlist").append('<option value="University of Botswana-Department of Environmental Sciences">');
					$("#institutionlist").append('<option value="Okavango Research Institute-University of Botswana">');
					$("#institutionlist").append('<option value="Department of Meteorological Services">');
					$("#institutionlist").append('<option value="Botswana College of Agriculture">');
					break;
                                case "Germany" :
                                        $("#institutionlist").empty();
					$("#institutionlist").append('<option value="University of Hamburg">');
					$("#institutionlist").append('<option value="University of Jena">');
					$("#institutionlist").append('<option value="University of Trier">');
					$("#institutionlist").append('<option value="University of Bonn">');
					break;
                                case "Namibia" :
                                        $("#institutionlist").empty();
					$("#institutionlist").append('<option value="Namibia University of Science and Technology">');
					$("#institutionlist").append('<option value="University of Namibia">');
					$("#institutionlist").append('<option value="Namibia Ministry of Agriculture, Water and Forestry">');
					break;
                                case "South Africa" :
                                        $("#institutionlist").empty();
					$("#institutionlist").append('<option value="National Research Foundation">');
					$("#institutionlist").append('<option value="Department of Science and Technology">');
					$("#institutionlist").append('<option value="University of Stellenbosch">');
					break;
                                case "Zambia" :
                                        $("#institutionlist").empty();
					$("#institutionlist").append('<option value="University of Zambia">');
					$("#institutionlist").append('<option value="Department of Forestry Zambia Meteorological Department">');
					$("#institutionlist").append('<option value="Community Based Natural Resource Management">');
					$("#institutionlist").append('<option value="National Resource Sensing Centre (Zambia)">');
					$("#institutionlist").append('<option value="Zambia Wildlife Authority">');
					$("#institutionlist").append('<option value="Mulungushi University">');
					$("#institutionlist").append('<option value="Zambia Agriculture Research Institute">');
					break;

			}
		});

        	$("#owner").load("load_users.php?database=budget", function(){
			//do nothing?
	        });

		$("#cancel").click(function() { 
			window.location.href = "http://caprivi.sasscal.org/budget/index.php";	
		});

		$("#save").click(function() { 
                	$.post("create_task.php",
                        {
				database: "budget",
                                taskid: $("#taskid").val(),
                                description: $("#description").val(),
                                owner: $("#owner").val(),
                                currency: $("#currency").val(),
                                country: $("#country").val(),
                                institution: $("#institution").val(),
                                thematic_area: $("#thematic_area").val(),
                                investments_budget: $("#investments_budget").val(),
                                services_budget: $("#services_budget").val(),
                                consumables_budget: $("#consumables_budget").val(),
                                transport_budget: $("#transport_budget").val(),
                                personnel_budget: $("#personnel_budget").val(),
                                firstyear: $("#firstyear").val(),
                                firstquarter: $("#firstquarter").val()
                        },
                        function(data, status){
                                if (data == "OK") {
                                                $("#submit_message").html("Saved");
                                } else {
                                        $("#submit_message").html("<span style=\"color:red\">"+data+"</span>");
                                }
                                setTimeout(function() {$("#submit_message").html("");}, 3000);
                        });

		});
        });
    </script>
    <style  type="text/css">
#submit_message {
  padding-left: 10px;
  font-size: 12px;
  font-weight: bold;
  color: green;
}
  </style>
  </head>
  <body>
    <p  style="text-align: center;"><span  style="font-family: Helvetica,Arial,sans-serif; font-size: 30px;">
	Create New Task</span></p>

    <p>
    <table  border="0">
      <tbody id="task">
        <tr>
	  <td> Task ID </td>
	  <td> <input type="text" id="taskid"/>
	  </td>
	</tr>
        <tr>
	  <td> Description </td>
	  <td> <input type="text" id="description"/>
	  </td>
	</tr>
        <tr>
	  <td> Principle Investigator </td>
	  <td> <select id="owner">
		<option>Loading...</option>
	       </select>
	  </td>
	</tr>
        <tr>
	  <td> Institution </td>
	  <td> 
		<input list="institutionlist" id="institution">
		<datalist id="institutionlist">
  		   <option value="Please Select Country">
		   <!-- BOTSWANA --
  		   <option value="University of Botswana-Department of Environmental Sciences">
  		   <option value="Okavango Research Institute-University of Botswana">
  		   <option value="Department of Meteorological Services">
  		   <option value="Botswana College of Agriculture">
		   <!-- ANGOLA --
  		   <option value="FC-Universidade Agostinho Neto">
  		   <option value="FCA-Universidade Jose Eduardo dos Santos">
  		   <option value="CNIC-Centro Nacional de Investigaçao Cientifica (Ministerio de Ciencias e Tecnologia)">
  		   <option value="ISCE-Huila">
  		   <option value="ISPT-Huila">
		   <!-- ZAMBIA --
  		   <option value="University of Zambia">
  		   <option value="Department of Forestry Zambia Meteorological Department">
  		   <option value="Community Based Natural Resource Management">
  		   <option value="National Resource Sensing Centre (Zambia)">
  		   <option value="Zambia Wildlife Authority">
  		   <option value="Mulungushi University">
  		   <option value="Zambia Agriculture Research Institute">
		   <!-- NAMIBIA --
		   <!-- SOUTH AFRICA -->
		</datalist>
	  </td>
	</tr>
        <tr>
	  <td> Local Currency </td>
	  <td> 
    		<select  id="currency">
      		   <option  value="AOA">Angolan Kwanza (AOA)</option>
      		   <option  value="BWP">Botswana Pula</option>
      		   <option  value="NAD">Namibian Dollar (NAD)</option>
      		   <option  value="USD">US Dollar</option>
      		   <option  value="ZAR">South African Rand</option>
      		   <option  value="ZMW">Zambian Kwacha</option>
		</select>
	  </td>
	</tr>
        <tr>
	  <td> Country </td>
	  <td> 
    		<select id="country">
      		   <option>Angola</option>
      		   <option>Botswana</option>
      		   <option>Germany</option>
      		   <option>Namibia</option>
      		   <option>South Africa</option>
      		   <option>Zambia</option>
		</select>
	  </td>
	</tr>
        <tr>
	  <td> Thematic Area </td>
	  <td> 
    		<select id="thematic_area">
      		   <option>Climate</option>
      		   <option>Water</option>
      		   <option>Biodiversity</option>
      		   <option>Forestry</option>
      		   <option>Agriculture</option>
      		   <option>Capacity Building</option>
		</select>
	  </td>
	</tr>
        <tr>
	  <td> Investments Budget </td>
	  <td> <input type="text" id="investments_budget"/>
	  </td>
	</tr>
        <tr>
	  <td> Services Budget </td>
	  <td> <input type="text" id="services_budget"/>
	  </td>
	</tr>
        <tr>
	  <td> Consumables Budget </td>
	  <td> <input type="text" id="consumables_budget"/>
	  </td>
	</tr>
        <tr>
	  <td> Transport Budget </td>
	  <td> <input type="text" id="transport_budget"/>
	  </td>
	</tr>
        <tr>
	  <td> Personnel Budget </td>
	  <td> <input type="text" id="personnel_budget"/>
	  </td>
	</tr>
        <tr>
	  <td> Starting Year </td>
	  <td>
    		<select id="firstyear">
      		   <option  value="2010">2010</option>
      		   <option  value="2011">2011</option>
      		   <option  value="2012">2012</option>
      		   <option  value="2013">2013</option>
      		   <option  value="2014">2014</option>
      		   <option  value="2015">2015</option>
      		   <option  value="2016">2016</option>
      		   <option  value="2017">2017</option>
      		   <option  value="2018">2018</option>
		</select>
	  </td>
	</tr>
        <tr>
	  <td> Starting Quarter </td>
	  <td>
    		<select  id="firstquarter">
      		   <option  value="1">Q1</option>
      		   <option  value="2">Q2</option>
      		   <option  value="3">Q3</option>
      		   <option  value="4">Q4</option>
		</select>
	  </td>
	</tr>
    </table>
    </p>
    <p>
    <table  border="0">
      <tbody id="in_table">
        <tr>
          <td  style="text-align: right;"></td>
             <input type="button" id="save" value="Create"/>
             <input type="button" id="cancel" value="< Back"/>
	     <span id="submit_message"></span>
          </td>
        </tr>
      </tbody>
    </table>
    </p>
  </body>
</html>




