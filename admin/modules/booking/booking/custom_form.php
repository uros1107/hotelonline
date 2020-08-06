<script>
    $(function(){
		// variables init
        var ex_tax_input = $('#booking_ex_tax_0_0');
        var total_input = $('#booking_total_0_0');
        var tax_input = $('#booking_tax_amount_0_0');
        var balance_input = $('#booking_balance_0_0');
        var paid_input = $('#booking_paid_0_0');
        var discount_input = $('#booking_discount_0_0');
        var from_date_input = $('[name="booking_from_date_0[0][date]"]');
        var to_date_input = $('[name="booking_to_date_0[0][date]"]');
        var nnights_input = $('#booking_nights_0_0');
        var adults_input = $('#booking_adults_0_0');
        var children_input = $('#booking_children_0_0');
		
		// updates number of nights
		function updateNights(){
			var from_date = new Date(from_date_input.val());
			from_date = Date.UTC(from_date.getUTCFullYear(), from_date.getUTCMonth(), from_date.getUTCDate(), 0, 0, 0)/1000;
			
			var to_date = new Date(to_date_input.val());
			to_date = Date.UTC(to_date.getUTCFullYear(), to_date.getUTCMonth(), to_date.getUTCDate(), 0, 0, 0)/1000;
			
			var nnights = Math.ceil((to_date-from_date)/86400);
			if(nnights < 0) nnights = 0;
			
			setNumericInput(nnights_input, nnights);
		}
		to_date_input.on('change keyup', function(){
			updateNights();
		});
		from_date_input.on('change keyup', function(){
			updateNights();
		});
		updateNights();
    
		// updates ex total, total, tax amount and balance fields on rooms/activities/services update
        $('#table_booking_room, #table_booking_activity, #table_booking_service').on('change', '[name^=booking_room_amount_0],[name^=booking_activity_amount_0],[name^=booking_service_amount_0]', function(){
            var total = 0;
            var tax_amount = 0;
            var ex_tax = 0;
            var val_total = 0;
            var id = 0;
            var tax_rate = 0;
            $('[name^=booking_room_amount_0]').each(function(){
                val_total = getNumericInput($(this));
				id = $(this).attr('name').match(/\[([^[]*)\]/)[1];
				tax_rate = getNumericInput($('#booking_room_tax_rate_0_'+id));
				if(tax_rate >= 0 && val_total >= 0) ex_tax += val_total/(1+(tax_rate/100));
				total += val_total;
            });
            $('[name^=booking_activity_amount_0]').each(function(){
                val_total = getNumericInput($(this));
				id = $(this).attr('name').match(/\[([^[]*)\]/)[1];
				tax_rate = getNumericInput($('#booking_activity_tax_rate_0_'+id));
				if(tax_rate >= 0 && val_total >= 0) ex_tax += val_total/(1+(tax_rate/100));
				total += val_total;
            });
            $('[name^=booking_service_amount_0]').each(function(){
                val_total = getNumericInput($(this));
				id = $(this).attr('name').match(/\[([^[]*)\]/)[1];
				tax_rate = getNumericInput($('#booking_service_tax_rate_0_'+id));
				if(tax_rate >= 0 && val_total >= 0) ex_tax += val_total/(1+(tax_rate/100));
				total += val_total;
            });
			var tax_amount = total-ex_tax;
            total -= getNumericInput(discount_input);
			
            setNumericInput(total_input, total);
			if(tax_amount > 0) tax_input.val(Math.round(tax_amount*100)/100); else tax_input.val(0);
            setNumericInput(ex_tax_input, ex_tax);
			
            var paid = getNumericInput(paid_input);
            if(paid >= 0 && total >= 0 && total >= paid){
				var balance = total-paid;
				setNumericInput(balance_input, balance);
				if(balance == 0)
					$('[name="booking_status_0[0]"]').val('4');
			}
        });
        $('#table_booking_room').on('keyup', '[name^=booking_room_tax_rate_0],[name^=booking_room_amount_0]', function(){
            
            var id = $(this).attr('name').match(/\[([^[]*)\]/)[1];
            var tax_rate = getNumericInput($('#booking_room_tax_rate_0_'+id));
            var total = getNumericInput($('#booking_room_amount_0_'+id));
            
            if(tax_rate >= 0 && total >= 0){
                var ex_tax = total/(1+(tax_rate/100));
                var tax_amount = total-ex_tax;
            
                $('#booking_room_amount_0_'+id).trigger('change');
            }
        });
        $('#table_booking_activity').on('keyup', '[name^=booking_activity_tax_rate_0],[name^=booking_activity_amount_0]', function(){
            
            var id = $(this).attr('name').match(/\[([^[]*)\]/)[1];
            var tax_rate = getNumericInput($('#booking_activity_tax_rate_0_'+id));
            var total = getNumericInput($('#booking_activity_amount_0_'+id));
            
            if(tax_rate >= 0 && total >= 0){
                var ex_tax = total/(1+(tax_rate/100));
                var tax_amount = total-ex_tax;
            
                $('#booking_activity_amount_0_'+id).trigger('change');
            }
        });
        $('#table_booking_service').on('keyup', '[name^=booking_service_tax_rate_0],[name^=booking_service_amount_0]', function(){
            
            var id = $(this).attr('name').match(/\[([^[]*)\]/)[1];
            var tax_rate = getNumericInput($('#booking_service_tax_rate_0_'+id));
            var total = getNumericInput($('#booking_service_amount_0_'+id));
            
            if(tax_rate >= 0 && total >= 0){
                var ex_tax = total/(1+(tax_rate/100));
                var tax_amount = total-ex_tax;
            
                $('#booking_service_amount_0_'+id).trigger('change');
            }
        });
		
		// trigger amount update on discount update
		discount_input.one('change keyup', function(){
			$('[name^=booking_room_amount_0]:first').trigger('change'); 
		});
		
		// updates paid and balance fields on payments update
        $('#table_booking_payment').one('change keyup', '[name^=booking_payment_amount_0]', function(){
            var paid = 0;
            $('[name^=booking_payment_amount_0]').each(function(){
                val = getNumericInput($(this));
                if(val > 0) paid += val;
            });
            var total = getNumericInput(total_input);
            setNumericInput(paid_input, paid);
            if(paid >= 0 && total >= 0 && total >= paid){
				var balance = total-paid;
				setNumericInput(balance_input, balance);
				if(balance == 0)
					$('[name="booking_status_0[0]"]').val('4');
			}
        });
        
		// updates total adults on adults/room update
		$('#table_booking_room').one('change keyup', '[name^=booking_room_adults_0]', function(){
			var adults = 0;
            $('[name^=booking_room_adults_0]').each(function(){
                val = getNumericInput($(this));
                if(val > 0) adults += val;
			});
			setNumericInput(adults_input, adults);
        });
		
		// updates total children on children/room update
        $('#table_booking_room').one('change keyup', '[name^=booking_room_children_0]', function(){
			var children = 0;
            $('[name^=booking_room_children_0]').each(function(){
                val = getNumericInput($(this));
                if(val > 0) children += val;
			});
			setNumericInput(children_input, children);
        });
		
		// trigger amounts/adults/children update on page load
        $('#booking_room_amount_0_0,#booking_payment_amount_0_0,#booking_room_adults_0_0,#booking_room_children_0_0').trigger('change');
		
		// fills the customer fields on customer select
		$('[name="booking_id_user_0[0]"]').on('change', function(){
			var user_id = $(this).val();
			if(user_id > 0){
				$.ajax({
                    type: 'POST',
                    url: 'get_customer.php',
                    data: 'id='+user_id,
                    success: function(data){
						var data = $.parseJSON(data);
						
						if(data.id !== undefined){
							$('#booking_firstname_0_0').val(data.firstname);
							$('#booking_lastname_0_0').val(data.lastname);
							$('#booking_email_0_0').val(data.email);
							$('#booking_company_0_0').val(data.company);
							$('#booking_address_0_0').val(data.address);
							$('#booking_postcode_0_0').val(data.postcode);
							$('#booking_city_0_0').val(data.city);
							$('#booking_phone_0_0').val(data.phone);
							$('#booking_mobile_0_0').val(data.mobile);
							$('#booking_country_0_0').val(data.country);
						}
                    }
                });
			}
		});
		
		// fills the room fields on room select
		$('#table_booking_room').on('change', '[name^="booking_room_id_room_0"]', function(){
			var input = $(this);
			var room_id = input.val();
			if(room_id > 0){
				$.ajax({
                    type: 'POST',
                    url: 'get_room.php',
                    data: 'id='+room_id,
                    success: function(data){
						var data = $.parseJSON(data);
						
						if(data.id !== undefined){
							var id = input.attr('name').match(/\[([^[]*)\]/)[1];
			
							$('#booking_room_title_0_'+id).val(data.title);
							$('#booking_room_tax_rate_0_'+id).val(data.tax_rate);
						}
                    }
                });
			}
		});
		
		// fills the activity fields on activity select
		$('#table_booking_activity').on('change', '[name^="booking_activity_id_activity_0"]', function(){
			var input = $(this);
			var activity_id = input.val();
			if(activity_id > 0){
				$.ajax({
                    type: 'POST',
                    url: 'get_activity.php',
                    data: 'id='+activity_id,
                    success: function(data){
						var data = $.parseJSON(data);
						
						if(data.id !== undefined){
							var id = input.attr('name').match(/\[([^[]*)\]/)[1];
			
							$('#booking_activity_title_0_'+id).val(data.title);
							$('#booking_activity_duration_0_'+id).val(data.duration);
							$('#booking_activity_tax_rate_0_'+id).val(data.tax_rate);
						}
                    }
                });
			}
		});
    });
</script>
