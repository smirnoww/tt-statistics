function ratingModuleInit() {
	// сделаем аккордион
	$( "#accordionRating" ).accordion({ heightStyle : "content" });
	
	// проставим количество игроков в каждой группе аккордиона
	playerAmountRecalc();
	
	// обработка фильтра
	$('#playerFilter').on("input",	function(){ 
										var filterVal = $('#playerFilter').val().toLowerCase();
										$('table.ratingTable tr').show();
										if (filterVal.length>2) {
											$('table.ratingTable').find('tr td:nth-child(2)').each(	function() {
																										
																										// alert( $(this).prev().prop('nodeName')+' - '+$( this ).text()+' - '+$(this).find('a').first().text() );
																										if (!$(this).find('a').first().text().toLowerCase().includes(filterVal)) {
																											$(this).parents('tr').first().hide();
																										}
																									});
										}
										// пересчёт количества игроков в группах после фильтрации
										playerAmountRecalc();
									});
}

// пересчёт количества игроков в группах
function playerAmountRecalc() {
	$('table.ratingTable').each(function() {
		$('#'+$( this ).attr('ratingRange')).text('( '+$( this ).find('tr:not([style="display: none;"])').length+' )');
	});
}

//# sourceURL=ratingModule.js