@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Welcome</div>

                <div class="panel-body" id="app">

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Recipient</th>
                                <th>Address</th>
                                <th>Phone</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="order in orders">
                                <td>@{{ order.recipient }}</td>
                                <td>@{{ order.address }}</td>
                                <td>@{{ order.phone }}</td>
                            </tr>                            
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    var config = {
        apiKey: "AIzaSyCK1faJZ00oq4FtxIbBSqoIbYbP1rIWG5M",
        authDomain: "agenpos-6911a.firebaseapp.com",
        databaseURL: "https://agenpos-6911a.firebaseio.com",
        storageBucket: "agenpos-6911a.appspot.com",
      };
    firebase.initializeApp(config);

    new Vue({
        el: '#app',
        data:{
            hello: 'Hello Dolly !!'
        },
        methods:{
            reverseMessage: function(){
                this.hello = this.hello.split('').reverse().join('');
            }
        },
        firebase:{
            items: firebase.database().ref('cities'),
            orders: firebase.database().ref('orders').orderByChild('reverseCreated')
        }
        /*
        ready:function(){
            var mRef = ;

            console.log(mRef);

            mRef.on( 'child_added', function(dataSnapshot){
                console.log(dataSnapshot.val() );
                this.items.push(dataSnapshot.val());
            });
        }*/
    });    


</script>

@endsection
