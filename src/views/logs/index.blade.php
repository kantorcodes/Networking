@extends('layout.main')

@section('content')

<table class="table table-responsive table-bordered">
    <thead>
      <tr>
         <th>Endpoint Url</th>
         <th>Response Status Code</th>
         <th>Response Body</th>
         <th>Response Cookies</th>
         <th>Response Headers</th>
         </tr>
         </thead>
         <tbody>

         @if(!empty($requests))

             @foreach($requests->getCollection()->all() as $request)




             @endforeach
         @endif

       </tbody>
</table>
@stop