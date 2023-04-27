window.addEventListener('load', function () {  

	
	const typeOfLoading = document.getElementById('dbfwp_type1')
	const dbfwpInputElement1 = document.querySelectorAll('.wporg_row1')

	const valueSelected = typeOfLoading.options[typeOfLoading.selectedIndex].value
	console.log(valueSelected)
	if ( valueSelected == 'opacity') {
	
		dbfwpInputElement1.forEach((element) => {
			element.style.display = 'none'
		});
		
		//console.log(typeof typeOfLoading.value)
	} else if ( valueSelected == 'icon') {
		dbfwpInputElement1.forEach((element) => {
			element.style.display = 'table-row'
		});
	}
	//const valueSelected = typeOfLoading.options[typeOfLoading.selectedIndex].value



	typeOfLoading.addEventListener('change', function() {
		const valueSelected = typeOfLoading.options[typeOfLoading.selectedIndex].value
		console.log(valueSelected)
		if ( valueSelected == 'opacity') {
		
			dbfwpInputElement1.forEach((element) => {
				element.style.display = 'none'
			});
			
			//console.log(typeof typeOfLoading.value)
		} else if ( valueSelected == 'icon') {
			dbfwpInputElement1.forEach((element) => {
				element.style.display = 'table-row'
			});
		}
	});
})

jQuery(document).ready(function($){
    $('.my-color-field').wpColorPicker();
    $('.my-bg-color-field').wpColorPicker();
});