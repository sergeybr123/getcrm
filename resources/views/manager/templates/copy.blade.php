@extends('layouts.app')

@section('title', __('Шаблоны'))

@section('content')

@endsection
@section('scripts')
    <script>

        var bot = {!!json_encode($bot)!!};
        var listeners = {!!json_encode($listeners)!!};

        var botable_id = bot[0].botable_id;
        var botable_type = bot[0].botable_type;
        var description = bot[0].description;
        var type = bot[0].type;
        var name = bot[0].name;
        var active = bot[0].active;
        var style = JSON.parse(bot[0].style);

        var x = style.bg.x;
        var y = style.bg.y;
        var image = style.bg.image;
        var avatar = style.avatar;

        var new_style = {};

        if (x !== undefined && y !== undefined) {
            new_style = {
                bg: {
                    x: x,
                    y: y,
                    image: image,
                }
            }
        }
        if(image !== undefined) {
            new_style.bg = {image: image};
        }
        if(avatar !== undefined) {
            new_style.avatar = avatar;
        }

        var new_bot = {
            type: type,
            name: name,
            active: active,
            description: description,
            botable_type: botable_type,
            botable_id: botable_id, /*Сюда вставить ИД новой компании*/
            style: new_style,
        };

        var listeners = bot[0].listeners;

        var vr = {};

        listeners.forEach(function(element) {
            console.log(element);
        });


        console.log(listeners.length);

        console.log(new_bot);
        // console.log(new_style);
        console.log(listeners);
        console.log(bot);
        // console.log(listeners);
        // $.each(listeners, function (i, v) {
        //     console.log(v);
        // });


    </script>
@endsection