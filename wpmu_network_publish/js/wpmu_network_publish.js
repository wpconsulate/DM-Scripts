jQuery(document).ready(function($) {
	window.broadcast = {
		init: function(){
			
			// Allow blogs to be mass selected, unselected.							
			$("#__broadcast__broadcast_group").change(function(){
				var blogs = $(this).val().split(" ");
				for (var counter=0; counter < blogs.length; counter++)
				{
					$("#__broadcast__groups__666__" + blogs[counter] ).attr("checked", true);
				}
				$("#__broadcast_group").val("");
			});
			
			// React to changes to the tags click.
			if ( !$("#__broadcast__tags").attr("checked") )
				$("p.broadcast_input_tags_create").hide();
				
			$("#__broadcast__tags").change(function(){
				$("p.broadcast_input_tags_create").animate({
					height: "toggle"
				});
			});
			
			// React to changes to the taxonomies click.
			if ( !$("#__broadcast__taxonomies").attr("checked") )
				$("p.broadcast_input_taxonomies_create").hide();
				
			$("#__broadcast__taxonomies").change(function(){
				$("p.broadcast_input_taxonomies_create").animate({
					height: "toggle"
				});
			});
			
		}
	};
	
	broadcast.init();

	$broadcast = $("#WPMU_Network_publish");

	
	var blog_count = $( ".blogs ul.broadcast_blogs li", $broadcast ).length;
	
	
	if ( $( "input", $broadcast ).length < 1 )
		return;
	
	// Select all / none
	$broadcast.append(" \
		<p class=\"selection_change select_deselect_all\">" + broadcast_strings.select_deselect_all + "</p> \
		<p class=\"selection_change invert_selection\">" + broadcast_strings.invert_selection + "</p>");

	$(".select_deselect_all").click(function(){
		var checkedStatus = ! $("wpmu_network_publish .broadcast_blogs .checkbox").attr("checked");
		$("wpmu_network_publish .broadcast_blogs .checkbox").each(function(key, value){
			if ( $(value).attr("disabled") != true)
				$(value).attr("checked", checkedStatus);
		});
	})

	$("wpmu_network_publish .invert_selection").click( function(){
			$.each( $(".broadcast_blogs input"), function(index, item){
					$(item).attr("checked", ! $(item).attr("checked") ); 
			});
	});
	
	// Need to hide the blog list?
	if ( blog_count > 5 )
	{
		$("wpmu_network_publish .broadcast_to").addClass("broadcast_to_opened").append("<div class=\"arrow_container\"><div class=\"arrow\"></div></div>");
		
		close_broadcasted_blogs( $("wpmu_network_publish .broadcast_to") );
		
		// Make it switchable!
		$("wpmu_network_publish .broadcast_to .arrow_container").click(function(){
			if ( $(this).parent().hasClass("broadcast_to_opened") )
				close_broadcasted_blogs( $(this).parent() );
			else
				open_broadcasted_blogs( $(this).parent() );
		});
		
	}
	
	/**
		Hides all the blogs ... except those that have been selected.
	**/
	function close_broadcasted_blogs( item )
	{
		// Close it up!
		$(item).removeClass("broadcast_to_opened");
		$(item).addClass("broadcast_to_closed");

		// Copy all selected blogs to the activated list
		$.each( $("wpmu_network_publish .blogs ul.broadcast_blogs li"), function (index,item){
			var checked = $("input", item).attr("checked");
			if ( ! checked )
				$(item).hide();
		}); 
	}
	
	/**
		Reshows all the hidden blogs.
	**/
	function open_broadcasted_blogs( item )
	{
		// Open it up!
		$(item).removeClass("broadcast_to_closed");
		$(item).addClass("broadcast_to_opened");
		
		$.each( $("wpmu_network_publish .blogs ul.broadcast_blogs li"), function(index, item){
			$(item).show();
		});
	}
    
    
});




jQuery.fn.vchecks = function() {
    
    object = jQuery(this);
    object.addClass('geogoer_vchecks');
    object.find("li:first").addClass('first');
    object.find("li:last").addClass('last');
    //removing checkboxes
    object.find("input[type=checkbox]").each(function(){
        jQuery(this).hide();
    });
    //adding images true false
    object.find("li").each(function(){
        if(jQuery(this).find("input[type=checkbox]").attr('checked') == 'checked'){
            jQuery(this).addClass('checked');
            jQuery(this).append('<div class="check_div"></div>');
        }
        else{
            jQuery(this).addClass('unchecked');
            jQuery(this).append('<div class="check_div"></div>');
        }
    });
    
    
    
    //binding onClick function
    object.find("li").find('label').click(function(e){
        e.preventDefault();
        check_li = jQuery(this).parent('li');
        checkbox = jQuery(this).parent('li').find("input[type=checkbox]");
        
        
        if(checkbox.attr('checked') == 'checked'){
            checkbox.attr('checked',false);
            checkbox.removeAttr('checked');
            check_li.removeClass('checked');
            check_li.addClass('unchecked');
        }
        else{
            checkbox.attr('checked' , 'checked');
            check_li.removeClass('unchecked');
            check_li.addClass('checked');

        }
    });
    
    
    //Added for Select All
    jQuery("#selectAllBlogs").click(function(){
        object.find("li").find('label').each(function(){
            jQuery(this).trigger('click');
        })
    });
    
    //mouse over / out
    //simple
    object.find("li:not(:last,:first)").find('span').bind('mouseover', function(e){
        jQuery(this).parent('li').addClass('hover');
    });
    object.find("li:not(:last,:first)").find('span').bind('mouseout', function(e){
        jQuery(this).parent('li').removeClass('hover');
    });
    //first
    object.find("li:first").find('span').bind('mouseover', function(e){
        jQuery(this).parent('li').addClass('first_hover');
    });
    object.find("li:first").find('span').bind('mouseout', function(e){
        jQuery(this).parent('li').removeClass('first_hover');
    });
    //last
    object.find("li:last").find('span').bind('mouseover', function(e){
        jQuery(this).parent('li').addClass('last_hover');
    });
    object.find("li:last").find('span').bind('mouseout', function(e){
        jQuery(this).parent('li').removeClass('last_hover');
    });
}


jQuery(function(){
    jQuery("ul.broadcast_blogs").vchecks();
});