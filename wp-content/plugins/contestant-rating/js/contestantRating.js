/*global CR_isWorking CR_current_post jQuery */
function CR_star_over(obj, star_number) {
	var cr=obj.parentNode;
	var as=cr.getElementsByTagName('label');
	for(var i=0;i<star_number;++i) {
		as[i].lastClass = as[i].className;
		as[i].className = 'CR_full_star';
	}
	for(;i<as.length;++i) {
		as[i].lastClass = as[i].className;
	}
}
/*function CR_star_over(star_number)
{
	var as=jQuery('.CR_stars label');
	for(var i=0; i<star_number; i++)
	{
		as[i].lastClass=jQuery(as[i]).attr('class');
		jQuery(as[i]).addClass('CR_full_star');
	}
	for (;i<as.length;++i)
	{
		as[i].lastClass=jQuery(as[i]).attr('class');
		//jQuery(as[i]).addClass('CR_no_star');
	}
}*/
function CR_star_out(obj) {
	var bs=obj.getElementsByTagName('label');
	for (var j=0;j<bs.length; j++)
	{
		if (bs[j].lastClass) {
			bs[j].className = bs[j].lastClass;
		}
	}
}
function CR_save_vote(post, points)
{
	if(!CR_isWorking)
	{
		CR_current_post=post;
		jQuery.get(post_ajax_star, {p: CR_current_post, cr_stars: points}, function(html){
			CR_isWorking=false;
			jQuery('.CR_container').html(html);
		});
	}
}
jQuery(function(){
	jQuery('label[for^=cr_star_]').click(function(){
		var cr_star=jQuery(this).attr('for');
		var voteValue=cr_star.split('_');
		var post=voteValue[2];
		var point=voteValue[3];
		CR_save_vote(post, point);
	});
	jQuery('label[for^=cr_star_]').mouseover(function(){
		var cr_star_over=jQuery(this).attr('for');
		var starValue=cr_star_over.split('_');
		var points=starValue[3];
		CR_star_over(this, points);
	});
});
CR_current_post = null;
CR_isWorking=false;