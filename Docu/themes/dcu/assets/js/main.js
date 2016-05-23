(function($) {
    loadTwitter = function(){
        $.getJSON("/index.php?r=site/tweets",
            function(data){
                if (data) {
                    $("#twitter").empty();

                    for (var i = 0; i < data.length; i++) {
                      var text = data[i].text;
                        $.each(data[i].entities, function(type, entries){
                            if(type == 'hashtags'){
                                $.each(entries, function(i, h){
                                    hashtag = "#"+h.text;
                                    newtext = text.replace(hashtag, "<a target=\"_blank\" href=\"http://twitter.com/search?q=%23"+h.text+"&src=hash\">"+hashtag+"</a>");
                                    text = newtext;
                                });
                            }
                            if(type == 'urls'){
                                $.each(entries, function(i, h){
                                    newtext = text.replace(h.url, "<a target=\"_blank\" href=\""+h.expanded_url+"\">"+h.url+"</a>");
                                    text = newtext;
                                });
                            }
                            if(type == 'media'){
                                $.each(entries, function(i, h){
                                    newtext = text.replace(h.url, "<a target=\"_blank\" href=\""+h.media_url+"\">"+h.url+"</a>");
                                    text = newtext;
                                });
                            }
                            if(type == 'user_mentions'){
                                $.each(entries, function(i, h){
                                    handle = "@"+h.screen_name;
                                    newtext = text.replace(handle, "<a target=\"_blank\" href=\"http://twitter.com/"+h.screen_name+"\">"+handle+"</a>");
                                    text = newtext;
                                });
                            }          
                        });

                        $("#twitter").append("<div class=\"tweet\"><span>"+text+"</span></div>");  
                    }
              	}
            }
        );
    }

    $(document).on("click", "#selector-button", function(e){
        if($(e.target).data("toggle") == true){
            $(e.target).data("toggle", false);

             $("#selection-widget").hide();

            //Verwijder events
            $(document).off("mouseenter", "a.swipebox");
            $(document).off("mouseleave", "a.swipebox");
            $(document).off("click", "span.select-this");
            $(".documentList").find("a.swipebox").swipebox();
        }else{
            $(e.target).data("toggle", true);

            $(document).off("click", "a.swipebox");

            $("a.swipebox").append("<span class=\"select-this\"/>");

            $("#selection-widget").show();
            
            $(document).on("click", "span.select-this", function(e){
                console.log($(this).parent("a").data("itemid"));
                $("#selection-widget table tbody").append(
                    "<tr>"+
                        "<td><i class=\"icon-file icon-white\"></i> Document</td>"+
                        "<td data-itemid=\""+$(this).parent("a").data("itemid")+"\">"+$(this).parent("a").title()+"</td>"+
                        "<td><i class=\"icon-trash icon-white\"></i></td>"+
                    "</tr>"
                );
                e.preventDefault();
            });

            $(document).on("mouseenter", "a.swipebox", function(){
                $(this).find("span.select-this").show();
            }).on("mouseleave", "a.swipebox", function(){
                $(this).find("span.select-this").hide();
            });
            
            $(document).on("click", "a.swipebox", function(e){
                e.preventDefault();
            });
        }
    });

    $(document).on("ready", function(){
    	loadTwitter();
    });
})(jQuery);

