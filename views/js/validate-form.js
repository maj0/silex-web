//https://www.sitepoint.com/basic-jquery-form-validation-tutorial/
// https://jqueryvalidation.org/jQuery.validator.addMethod/
// Wait for the DOM to be ready
$(function() {
  //alert('document ready');
  // Initialize form validation on the add_user form.
  // It has the name attribute "add_user"
  try{
	// organisation validatio
    $("form[name=organisation]").validate({
    // Specify validation rules
    rules: {
      // The key name on the left side is the name attribute
      // of an input field. Validation rules are defined
      // on the right side
      name: "required",
      address: "required",
    },
    // Specify validation error messages
    messages: {
      name: "Please enter organisation name",
      address: "Please enter organisation address",
    },
    // Make sure the form is submitted to the destination defined
    // in the "action" attribute of the form when valid
    submitHandler: function(form) {
      form.submit();
    }
  });
  // user validation
  $("form[name=user]").validate({
    // Specify validation rules
    rules: {
      // The key name on the left side is the name attribute
      // of an input field. Validation rules are defined
      // on the right side
      name: "required",
      email: {
        required: true,
        // Specify that email should be validated
        // by the built-in "email" rule
        email: true
      },
	  birthdate: {
		required: false,
		date: true
	  },
      /*password: {
        required: true,
        minlength: 6
      }*/
    },
    // Specify validation error messages
    messages: {
      name: "Please enter user name",
      password: {
        required: "Please provide a password",
        minlength: "Your password must be at least 6 characters long"
      },
      email: "Please enter a valid email address"
    },
    // Make sure the form is submitted to the destination defined
    // in the "action" attribute of the form when valid
    submitHandler: function(form) {
	  if(1<$('select[name=role]').val())
	  {
		  var val = $('input[name=organisation_ID]').val();
		  //alert('Organisation ID is empty,val='+val);
		  if(val=='')
		  {
			alert('Please enter Organisation ID');
			return false;
		  }
	  }
      form.submit();
    }
  });
  jQuery.validator.addMethod("organisation_ID", function(value, element) {
  // allow any non-whitespace characters as the host part
	if(1<$("select[name=role]").val())
	{
		//var val = $('input[name=organisation_ID]').val();
		if(value=='') return false;
	}
  	//alert('Organisation ID is empty,val='+value);
	if(value.length<1) return false;
	return true;
}, "Please provide a valid organisation_ID");
  jQuery.validator.addMethod("password", function(value, element) {
  // allow any non-whitespace characters as the host part
  if(value.length<6) return false;
  if(!(/(([A-Z]+)|([a-z]+)|[#!\$%&]+|([0-9]+)){6}/g.test(value))) return false;
  return true;
}, "Please provide a valid password containing 1 capital 1 lowercase 1 number 1 symbol(#!$%&) and at least 6 characters");
  jQuery.validator.addMethod("address", function(value, element) {
  // allow any non-whitespace characters as the host part
  if(value.length<6) return false;
  if(!(/(UK|Germany|France|Spain|Italy)/gi.test(value))) return false;
  return true;
}, "Please provide a valid country name");
  } catch(e) {
	alert('Validation Error:' + e);
  }
});