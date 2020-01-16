//$(document).ready(function() {
    require('./bootstrap');
    window.Vue = require('vue');

    Vue.component('example-component', require('./components/ExampleComponent.vue'));
    Vue.component('app-component', require('./components/AppComponent.vue'));

    const app = new Vue({
        el: '#app',
        data: {
            title: 'COMTEQ Registration System'
        },

    });
//});
/*
$(document).ready(function() {
    alert("Hello");
      // add form sched college
$('#Add_College_Sched_Form').hide();
  $('#Add_New_College_Sched_Btn').click(function(){
      $('#Add_College_Sched_Form').show();
      $('#Add_New_College_Sched_Btn').hide();
  });
  $('#Cancel_Sched_College_Btn').click(function(){
    $('#Add_College_Sched_Form').hide();
    $('#Add_New_College_Sched_Btn').show();
  });
  // add form sched SH
$('#Add_Sched_SH_Form').hide();
    $('#Add_New_Sched_SH_Btn').click(function(){
      $("#Add_Sched_SH_Form").show();
      $('#Add_New_Sched_SH_Btn').hide();
    });
    $('#Cancel_Sched_SH_Btn').click(function(){
      $('#Add_Sched_SH_Form').hide();
      $('#Add_New_Sched_SH_Btn').show();
    });
      // add form subject college
  $('#Add_College_Subject_Form').hide();
        $('#Add_New_College_Subject_Btn').click(function(){
          $("#Add_College_Subject_Form").show();
          $('#Add_New_College_Subject_Btn').hide();
        });
        $('#Cancel_College_Subject_Btn').click(function(){
          $('#Add_College_Subject_Form').hide();
          $('#Add_New_College_Subject_Btn').show();
      });
      // add form subject SH
  $('#Add_SH_Subject_Form').hide();
        $('#Add_New_SH_Subject_Btn').click(function(){
          $("#Add_SH_Subject_Form").show();
          $('#Add_New_SH_Subject_Btn').hide();
        });
        $('#Cancel_SH_Subject_Btn').click(function(){
          $('#Add_SH_Subject_Form').hide();
          $('#Add_New_SH_Subject_Btn').show();
        });
        // add form curriculum college
    $('#Collge_Curriculum_Nav_Tabs').hide();
    $('#Collge_Curriculum_Tab_Content').hide();
    $('#Add_College_Curriculum_Tab_Form').hide();
    $('#Add_College_Curriculum_Subject_Tab_Form').hide();
          $('#Add_New_College_Curriculum_Btn').click(function(){
            $('#Collge_Curriculum_Nav_Tabs').show();
            $('#Collge_Curriculum_Tab_Content').show();
            $('#Add_College_Curriculum_Tab_Form').show();
            $('#Add_Collge_Curriculum_Tab').click();
            $('#Add_New_College_Curriculum_Btn').hide();
          });
          $('#Add_Collge_Curriculum_Tab').click(function(){
            $('#Add_College_Curriculum_Tab_Form').show();
            $('#Add_College_Curriculum_Subject_Tab_Form').hide();
          });
          $('#Add_College_Curriculum_Subject_Tab').click(function(){
            $('#Add_College_Curriculum_Tab_Form').hide();
            $('#Add_College_Curriculum_Subject_Tab_Form').show();
          });
          $('#Next_College_Curriculum_Btn').click(function(){
            $('#Collge_Curriculum_Nav_Tabs').show();
            $('#Collge_Curriculum_Tab_Content').show();
            $('#Add_College_Curriculum_Subject_Tab').click();
            $('#Add_College_Curriculum_Subject_Tab_Form').show();
            $('#Add_College_Curriculum_Tab_Form').hide();
            $('#Add_New_College_Curriculum_Btn').hide();
          });
          $('#Previous_College_Curriculum_Btn').click(function(){
            $('#Collge_Curriculum_Nav_Tabs').show();
            $('#Collge_Curriculum_Tab_Content').show();
            $('#Add_College_Curriculum_Tab_Form').show();
            $('#Add_Collge_Curriculum_Tab').click();
            $('#Add_College_Curriculum_Subject_Tab_Form').hide();
            $('#Add_New_College_Curriculum_Btn').hide();
          });
          $('#Cancel_College_Curriculum_Btn').click(function(){
            $('#Collge_Curriculum_Nav_Tabs').hide();
            $('#Collge_Curriculum_Tab_Content').hide();
             $('#Add_College_Curriculum_Tab_Form').hide();
               $('#Add_New_College_Curriculum_Btn').show();
          });
          $('#Next_Tab_Curriculum_College_Btn').click(function(){
            $('#Manage_College_Curriculum_Tab').click();
          });
          $('#Previous_Tab_Curriculum_College').click(function(){
            $('#College_Edit_Curriculum_Tab').click();
          });
          // $('#Manage_Subject_Added_Curriculum_Form').hide();
          // $('#Manage_Subject_Available_Curriculum_Form').hide();
          // $('#Manage_College_Curriculum_Btn').click(function(){
          //   $('#Manage_Subject_Added_Curriculum_Form').show();
          //   $('#Manage_Subject_Available_Curriculum_Form').show();
          // });



          // add form currculum SH
          $('#SH_Curriculum_Nav_Tabs').hide();
          $('#SH_Curriculum_Tab_Content').hide();
          $('#Add_SH_Curriculum_Tab_Form').hide();
          $('#Add_SH_Curriculum_Subject_Tab_Form').hide();
                $('#Add_New_Curriculum_SH_btn').click(function(){
                  $('#SH_Curriculum_Nav_Tabs').show();
                  $('#Collge_Curriculum_Tab_Content').show();
                  $('#SH_Curriculum_Tab_Content').show();
                  $('#Add_SH_Curriculum_Tab').click();
                  $('#Add_New_Curriculum_SH_btn').hide();
                });
                $('#Add_SH_Curriculum_Tab').click(function(){
                  $('#Add_SH_Curriculum_Tab_Form').show();
                  $('#Add_SH_Curriculum_Subject_Tab_Form').hide();
                });
                $('#Add_SH_Curriculum_Subject_Tab').click(function(){
                  $('#Add_SH_Curriculum_Tab_Form').hide();
                  $('#Add_SH_Curriculum_Subject_Tab_Form').show();
                });
                $('#Next_SH_Curriculum_Btn').click(function(){
                  $('#SH_Curriculum_Nav_Tabs').show();
                  $('#SH_Curriculum_Tab_Content').show();
                  $('#Add_SH_Curriculum_Subject_Tab').click();
                  $('#Add_SH_Curriculum_Subject_Tab_Form').show();
                  $('#Add_SH_Curriculum_Tab_Form').hide();

                });
                $('#Previous_SH_Curriculum_Btn').click(function(){
                  $('#SH_Curriculum_Nav_Tabs').show();
                  $('#SH_Curriculum_Tab_Content').show();
                  $('#Add_SH_Curriculum_Tab_Form').show();
                  $('#Add_SH_Curriculum_Tab').click();
                  $('#Add_SH_Curriculum_Subject_Tab_Form').hide();
                  $('#Add_New_Curriculum_SH_btn').hide();
                });
                $('#Cancel_SH_Curriculum_Btn').click(function(){
                  $('#SH_Curriculum_Nav_Tabs').hide();
                  $('#SH_Curriculum_Tab_Content').hide();
                   $('#Add_SH_Curriculum_Tab_Form').hide();
                     $('#Add_New_Curriculum_SH_btn').show();
                });
                // $('#Manage_Subject_Added_Curriculum_Form').hide();
                // $('#Manage_Subject_Available_Curriculum_Form').hide();
                // $('#Manage_College_Curriculum_Btn').click(function(){
                //   $('#Manage_Subject_Added_Curriculum_Form').show();
                //   $('#Manage_Subject_Available_Curriculum_Form').show();
                // });
                $('#Next_Tab_Curriculum_SH_Btn').click(function(){
                  $('#Manage_SH_Curriculum_Tab').click();
                });
                $('#Previous_Tab_Curriculum_College').click(function(){
                  $('#SH_Edit_Curriculum_Tab').click();
                });





            // add Instructor
              $('#nav_tabs').hide();
              $('#tab_content').hide();
              $('#Add_New_Instructor_btn').click(function(){
                  $('#nav_tabs').show();
                  $('#tab_content').show();
                  $('#Add_PersonalInfo_li').click();
                $('#Add_New_Instructor_btn').hide();
              }); // end Add_New_Instructor_btn

              $('#Cancel_Instructor_Btn').click(function(){
                $('#nav_tabs').hide();
                $('#tab_content').hide();
                $("#Add_New_Instructor_btn").show();
              });// Cancel_Instructor_Btn

              $('#Next_Instructor_WorkExp_Btn').click(function(){
                $('#Add_Attainment_li').click();
              }); // Next_Instructor_WorkExp_Btn

              $('#Back_Instructor_Personal_Btn').click(function(){
                $('#Add_PersonalInfo_li').click();
              }); // Back_Instructor_Personal_Btn

              $('#Next_Instructor_Preffered_Btn').click(function(){
                  $('#Add_PrefferedSubject_li').click();
              }); // Next_Instructor_Preffered_Btn

              $('#Back_Instructor_WorkExp_Btn').click(function(){
                  $('#Add_Attainment_li').click();
              }); // Back_Instructor_WorkExp_Btn

              $('#Next_Instructor_TimeAvailability_Btn').click(function(){
                  $('#Add_TimeAvailability_').click();
              }); // Next_Instructor_TimeAvailability_Btn

              $('#Back_Instructor_Preffered_Btn').click(function(){
                $('#Add_PrefferedSubject_li').click();
              }); // Back_Instructor_Preffered_Btn
              $('#edit_nav_tabs').hide();
              $('#edit_tab_Content').hide();
              $('#edit_instruc_btn').click(function(){
                $('#edit_nav_tabs').show();
                $('#edit_tab_Content').show();
                $('#Edit_Personal_Li').click();
              $('#Edit_Instructor_Next_Attainment_Btn').click(function(){
                  $('#Edit_Attainment_Li').click();
                });
              $('#Edit_Instructor_Back_Personal_Btn').click(function(){
                  $('#Edit_Personal_Li').click();
                });
              $('#Edit_Instructor_Next_Preffered_Btn').click(function(){
                  $('#Edit_Preffered_Li').click();
                });
              $('#Edit_Instructor_Back_Attainment_Btn').click(function(){
                  $('#Edit_Attainment_Li').click();
                });
              $('#Edit_Instructor_Next_TimeAvailability_Btn').click(function(){
                  $('#Edit_TimeAvailability_Li').click();
                });
              $('#Edit_Instructor_Back_Preffered_Btn').click(function(){
                  $('#Edit_Preffered_Li').click();
                });
              });





});

$(document).ready(function (){
  // Monday
    $("#Add_Chk_Monday").click(function () {

      if($(this).is(":checked")){
        $("#Add_Txt_Monday_Start").removeAttr("disabled");
        $("#Add_Txt_Monday_End").removeAttr("disabled");
      }
      else{
         $("#Add_Txt_Monday_Start").attr("disabled", "disabled");
         $("#Add_Txt_Monday_End").attr("disabled", "disabled");
      }
    });
    // Tuesday

    $("#Add_Chk_Tuesday").click(function () {

      if($(this).is(":checked")){
        $("#Add_Txt_Tuesday_Start").removeAttr("disabled");
        $("#Add_Txt_Tuesday_End").removeAttr("disabled");
      }
      else{
         $("#Add_Txt_Tuesday_Start").attr("disabled", "disabled");
         $("#Add_Txt_Tuesday_End").attr("disabled", "disabled");
      }
    });
    // Wednesday
    $("#Add_Chk_Wednesday").click(function () {

      if($(this).is(":checked")){
        $("#Add_Txt_Wednesday_Start").removeAttr("disabled");
        $("#Add_Txt_Wednesday_End").removeAttr("disabled");
      }
      else{
         $("#Add_Txt_Wednesday_Start").attr("disabled", "disabled");
         $("#Add_Txt_Wednesday_End").attr("disabled", "disabled");
      }
    });
    // Thursday
    $("#Add_Chk_Thursday").click(function () {

      if($(this).is(":checked")){
        $("#Add_Txt_Thursday_Start").removeAttr("disabled");
        $("#Add_Txt_Thursday_End").removeAttr("disabled");
      }
      else{
         $("#Add_Txt_Thursday_Start").attr("disabled", "disabled");
         $("#Add_Txt_Thursday_End").attr("disabled", "disabled");
      }
    });
    // Friday
    $("#Add_Chk_Friday").click(function () {

      if($(this).is(":checked")){
        $("#Add_Txt_Friday_Start").removeAttr("disabled");
        $("#Add_Txt_Friday_End").removeAttr("disabled");
      }
      else{
         $("#Add_Txt_Friday_Start").attr("disabled", "disabled");
         $("#Add_Txt_Friday_End").attr("disabled", "disabled");
      }
    });
    // Saturday
    $("#Add_Chk_Saturday").click(function () {

      if($(this).is(":checked")){
        $("#Add_Txt_Saturday_Start").removeAttr("disabled");
        $("#Add_Txt_Saturday_End").removeAttr("disabled");
      }
      else{
         $("#Add_Txt_Saturday_Start").attr("disabled", "disabled");
         $("#Add_Txt_Saturday_End").attr("disabled", "disabled");
      }
    });

    // Monday
      $("#Chk_Monday").click(function () {

        if($(this).is(":checked")){
          $("#Txt_Monday_Start").removeAttr("disabled");
          $("#Txt_Monday_End").removeAttr("disabled");
        }
        else{
           $("#Txt_Monday_Start").attr("disabled", "disabled");
           $("#Txt_Monday_End").attr("disabled", "disabled");
        }
      });
      // Tuesday

      $("#Chk_Tuesday").click(function () {

        if($(this).is(":checked")){
          $("#Txt_Tuesday_Start").removeAttr("disabled");
          $("#Txt_Tuesday_End").removeAttr("disabled");
        }
        else{
           $("#Txt_Tuesday_Start").attr("disabled", "disabled");
           $("#Txt_Tuesday_End").attr("disabled", "disabled");
        }
      });
      // Wednesday
      $("#Chk_Wednesday").click(function () {

        if($(this).is(":checked")){
          $("#Txt_Wednesday_Start").removeAttr("disabled");
          $("#Txt_Wednesday_End").removeAttr("disabled");
        }
        else{
           $("#Txt_Wednesday_Start").attr("disabled", "disabled");
           $("#Txt_Wednesday_End").attr("disabled", "disabled");
        }
      });
      // Thursday
      $("#Chk_Thursday").click(function () {

        if($(this).is(":checked")){
          $("#Txt_Thursday_Start").removeAttr("disabled");
          $("#Txt_Thursday_End").removeAttr("disabled");
        }
        else{
           $("#Txt_Thursday_Start").attr("disabled", "disabled");
           $("#Txt_Thursday_End").attr("disabled", "disabled");
        }
      });
      // Friday
      $("#Chk_Friday").click(function () {

        if($(this).is(":checked")){
          $("#Txt_Friday_Start").removeAttr("disabled");
          $("#Txt_Friday_End").removeAttr("disabled");
        }
        else{
           $("#Txt_Friday_Start").attr("disabled", "disabled");
           $("#Txt_Friday_End").attr("disabled", "disabled");
        }
      });
      // Saturday
      $("#Chk_Saturday").click(function () {

        if($(this).is(":checked")){
          $("#Txt_Saturday_Start").removeAttr("disabled");
          $("#Txt_Saturday_End").removeAttr("disabled");
        }
        else{
           $("#Txt_Saturday_Start").attr("disabled", "disabled");
           $("#Txt_Saturday_End").attr("disabled", "disabled");
        }
      });

});


// college schedule verification
$(document).ready(function(){
  $('#Add_College_Sched_SuccessMessage').hide();
  $('#Yes_College_Verification_Btn').click(function(){
    $('#Add_College_Sched_SuccessMessage').show();
    $('#Add_College_Sched_Form').hide();
    $('#Add_New_College_Sched_Btn').show();
      window.setTimeout(function () {
        $("#Add_College_Sched_SuccessMessage").hide(); }, 2000);
  });
  $('#Update_College_Sched_SuccessMessage').hide();
  $('#Update_College_SuccessBtn').click(function(){
    $('#Update_College_Sched_SuccessMessage').show();
        window.setTimeout(function () {
          $("#Update_College_Sched_SuccessMessage").hide(); }, 2000);

    });
  $('#Delete_College_Sched_SuccessMessage').hide();
  $('#Delete_College_SuccessBtn').click(function(){
    $('#Delete_College_Sched_SuccessMessage').show();
    window.setTimeout(function () {
      $("#Delete_College_Sched_SuccessMessage").hide(); }, 2000);


    });
});

// SH Schedule verification

$(document).ready(function(){
  $('#Add_SH_Sched_SuccessMessage').hide();
  $('#Yes_SH_Verification_Btn').click(function(){
    $('#Add_SH_Sched_SuccessMessage').show();
    $('#Add_Sched_SH_Form').hide();
    $('#Add_New_Sched_SH_Btn').show();
    window.setTimeout(function () {
      $("#Add_SH_Sched_SuccessMessage").hide(); }, 2000);
  });
  $('#Update_SH_Sched_SuccessMessage').hide();
  $('#Update_SH_SuccessBtn').click(function(){
    $('#Update_SH_Sched_SuccessMessage').show();
    window.setTimeout(function () {
      $("#Update_SH_Sched_SuccessMessage").hide(); }, 2000);
  });
  $('#Delete_SH_Sched_SuccessMessage').hide();
  $('#Delete_SH_SuccessBtn').click(function(){
    $('#Delete_SH_Sched_SuccessMessage').show();
    window.setTimeout(function () {
      $("#Delete_SH_Sched_SuccessMessage").hide(); }, 2000);
    });
  $('#Add_SH_Subject_SuccessMessage').hide();
  $('#Yes_SH_subject_Verification_Btn').click(function(){
    $('#Add_SH_Subject_SuccessMessage').show();
    $('#Add_SH_Subject_Form').hide();
    $('#Add_New_SH_Subject_Btn').show();
    window.setTimeout(function () {
      $("#Add_SH_Subject_SuccessMessage").hide(); }, 2000);
    });
  });
  $('#Update_SH_Subject_SuccessMessage').hide();
  $('#Update_SH_Subject_SuccessBtn').click(function(){
    $('#Update_SH_Subject_SuccessMessage').show();
    window.setTimeout(function () {
      $("#Update_SH_Subject_SuccessMessage").hide(); }, 2000);
  });
  $('#Delete_SH_Subject_SuccessMessage').hide();
  $('#Delete_SH_Subject_SuccessBtn').click(function(){
      $('#Delete_SH_Subject_SuccessMessage').show();
      window.setTimeout(function () {
        $("#Delete_SH_Subject_SuccessMessage").hide(); }, 2000);
  });
  // Senior High Curriculum
  $('#Add_SH_Curriculum_SuccessMessage').hide();
$('#Yes_SH_Curriculum_Verification_Btn').click(function(){
  $('#Add_SH_Curriculum_SuccessMessage').show();
  $('#SH_Curriculum_Nav_Tabs').hide();
  $('#SH_Curriculum_Tab_Content').hide();
  $('#Add_SH_Curriculum_Tab_Form').hide();
  $('#Add_SH_Curriculum_Subject_Tab_Form').hide();
     $('#Add_New_Curriculum_SH_btn').show();
  window.setTimeout(function () {
    $("#Add_SH_Curriculum_SuccessMessage").hide(); }, 2000);
});
$('#Update_SH_Curriculum_SuccessMessage').hide();
$('#Update_SH_Curriculum_SuccessBtn').click(function(){
  $('#Update_SH_Curriculum_SuccessMessage').show();
  window.setTimeout(function () {
    $("#Update_SH_Curriculum_SuccessMessage").hide(); }, 2000);
});
$('#Delete_SH_Curriculum_SuccessMessage').hide();
$('#Delete_SH_Curriculum_SuccessBtn').click(function(){
  $('#Delete_SH_Curriculum_SuccessMessage').show();
  window.setTimeout(function () {
    $("#Delete_SH_Curriculum_SuccessMessage").hide(); }, 2000);
});
  // $('#LoginBtn').click(function(){
  //   alert('Successfully Login!');
  // });

// College Subject Verification
$(document).ready(function() {
  $('#Add_College_Subject_SuccessMessage').hide();
  $('#Yes_College_subject_Verification_Btn').click(function(){

    $('#Add_College_Subject_Form').hide();
    $('#Add_New_College_Subject_Btn').show();
    $('#Add_College_Subject_SuccessMessage').show();
    window.setTimeout(function () {
      $("#Add_College_Subject_SuccessMessage").hide(); }, 2000);

  });
  $('#Update_College_Subject_SuccessMessage').hide();
  $('#Update_College_Subject_SuccessBtn').click(function(){
      $('#Update_College_Subject_SuccessMessage').show();
      window.setTimeout(function () {
        $("#Update_College_Subject_SuccessMessage").hide(); }, 2000);
  });
  $('#Delete_College_Subject_SuccessMessage').hide();
  $('#Delete_College_Subject_SuccessBtn').click(function(){
    $('#Delete_College_Subject_SuccessMessage').show();
    window.setTimeout(function () {
      $("#Delete_College_Subject_SuccessMessage").hide(); }, 2000);
  });
    $('#Add_College_Curriculum_SuccessMessage').hide();
  $('#Yes_College_Curriculum_Verification_Btn').click(function(){
    $('#Add_College_Curriculum_SuccessMessage').show();
    $('#Collge_Curriculum_Nav_Tabs').hide();
    $('#Collge_Curriculum_Tab_Content').hide();
    $('#Add_College_Curriculum_Tab_Form').hide();
    $('#Add_College_Curriculum_Subject_Tab_Form').hide();
       $('#Add_New_College_Curriculum_Btn').show();
    window.setTimeout(function () {
      $("#Add_College_Curriculum_SuccessMessage").hide(); }, 2000);
  });
  $('#Update_College_Curriculum_SuccessMessage').hide();
  $('#Update_College_Curriculum_SuccessBtn').click(function(){
    $('#Update_College_Curriculum_SuccessMessage').show();
    window.setTimeout(function () {
      $("#Update_College_Curriculum_SuccessMessage").hide(); }, 2000);
  });
  $('#Delete_College_Curriculum_SuccessMessage').hide();
  $('#Delete_College_Curriculum_SuccessBtn').click(function(){
    $('#Delete_College_Curriculum_SuccessMessage').show();
    window.setTimeout(function () {
      $("#Delete_College_Curriculum_SuccessMessage").hide(); }, 2000);
  });



})

// instructor verification
$(document).ready(function(){
  $('#Add_Instructor_SuccessMessage').hide();
  $('#Yes_Instructor_Verification_Btn').click(function(){
    $('#Add_Instructor_SuccessMessage').show();
    $('#nav_tabs').hide();
    $('#tab_content').hide();
    $("#Add_New_Instructor_btn").show();
    window.setTimeout(function () {
      $("#Add_Instructor_SuccessMessage").hide(); }, 2000);
  });
  $('#Update_Instructor_SuccessMessage').hide();
  $('#Update_Instructor_Verification_SuccessBtn').click(function(){
    $('#Update_Instructor_SuccessMessage').show();
    window.setTimeout(function () {
      $("#Update_Instructor_SuccessMessage").hide(); }, 2000);
  });
  $('#Delete_Instructor_SuccessMessage').hide();

  $('#Delete_instructor_SuccessBtn').click(function(){
    $('#Delete_Instructor_SuccessMessage').show();
    window.setTimeout(function () {
      $("#Delete_Instructor_SuccessMessage").hide(); }, 2000);
  });
});


//print room Schedule
function printDiv(Room_Schedule) {
     var printContents = document.getElementById(Room_Schedule).innerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;
}

function printDiv(SH_Schedule) {
     var printContents = document.getElementById(SH_Schedule).innerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;
}

function printDiv(SH_Schedule) {
     var printContents = document.getElementById(SH_Schedule).innerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;
}

function printDiv(Instructor_Schedule) {
     var printContents = document.getElementById(Instructor_Schedule).innerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;
}

//AccountSettings
  $('#Update_Account_SuccessMessage').hide();
  $('#YesUpdate').click(function(){
    $('#Update_Account_SuccessMessage').show();
    window.setTimeout(function () {
      $("#Update_Account_SuccessMessage").hide(); }, 2000);
});
*/
