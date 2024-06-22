<!DOCTYPE html>
<html>
    
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Astroway</title>
</head>

<style type="text/css">
    body{
        font-family: 'DejaVu Sans; sans-serif;';
    }
    .m-0{
        margin: 0px;
    }
    .p-0{
        padding: 0px;
    }
    .pt-5{
        padding-top:5px;
    }
    .mt-10{
        margin-top:10px;
    }
    .text-center{
        text-align:center !important;
    }
    .w-100{
        width: 100%;
    }
    .w-50{
        width:50%;   
    }
    .w-85{
        width:85%;   
    }
    .w-15{
        width:15%;   
    }
    .logo img{
        width:65px;
        height:60px;        
    }
    .gray-color{
        color:#5D5D5D;
    }
    .text-bold{
        font-weight: bold;
    }
    .border{
        border:1px solid black;
    }
    table tr,th,td{
        border: 1px solid #d2d2d2;
        border-collapse:collapse;
        padding:7px 8px;
    }
    table tr th{
        background: #F4F4F4;
        font-size:15px;
    }
    table tr td{
        font-size:13px;
    }
    table{
        border-collapse:collapse;
    }
    .box-text p{
        line-height:10px;
    }
    .float-left{
        float:left;
    }
    .float-right{
        float:right;
    }
    .float-end{
        float:end;
    }
    .total-part{
        font-size:16px;
        line-height:12px;
    }
    .total-right p{
        padding-right:20px;
    }
</style>
<body>
<div class="head-title">
    <h1 class="text-center m-0 p-0">Invoice</h1>
</div>
<div class="add-detail mt-10">
    <div class="w-50 float-left mt-10">
        <p class="m-0 pt-5 text-bold w-100">Invoice Id - <span class="gray-color">#{{ $order->id }}</span></p>
        <p class="m-0 pt-5 text-bold w-100">Order Date - <span class="gray-color">{{ date('d-m-Y h:i', strtotime($order->created_at)) }}</span></p>
    </div>
    <div class="float-right logo mt-10">
        <img src="{{ url($logo->value) }}" alt="Logo" style="width: 60px; height: 60px">
        

    </div>
    <div style="clear: both;"></div>
</div>

<div class="table-section bill-tbl w-100 mt-10">
    <table class="table w-100 mt-10">
        <tr>
            <th class="w-50">Details</th>
            <th class="w-50">Address</th>
        </tr>
        <tr>
            <td>
                <div class="box-text ">
                    <p>Name : {{$order->userName}}</p>
                    <p>Email: {{ $order->userEmail }}</p>
                    <p>Contact: {{ $order->userContactNo }}</p>
                   
                </div>
            </td>
            <td>
                <div class="box-text">
                    <p> {{ $order->flatNo }},{{ $order->landmark }}</p>
                    <p>{{ $order->city }},{{ $order->state }}</p>
                    <p>{{ $order->country }}-{{ $order->pincode }}</p>                    
                   
                </div>
            </td>
        </tr>
    </table>
</div>
<div class="table-section bill-tbl w-100 mt-10">
    <table class="table w-100 mt-10">
        <tr>
            <th class="w-50">Payment Method</th>
        </tr>
        <tr>
            <td>Wallet</td>
        </tr>
    </table>
</div>
<div class="table-section bill-tbl w-100 mt-10">
    <table class="table w-100 mt-10">
        <tr>
            <th class="w-50">Category Name</th>
            <th class="w-50">Product Name</th>
            <th class="w-50">Product Image</th>
            <th class="w-50">Order Status</th>
            <th class="w-50">Subtotal</th>
            <th class="w-50">Tax Amount</th>
            <th class="w-50">Grand Total</th>
        </tr>
        
        <tr align="center">
            <td>{{ $order->categoryName }}</td>
            <td>{{ $order->productName }}</td>
            <td class="text-center">
                <div class="flex" style="align-items: center">
                    <div class="w-10 h-10 image-fit zoom-in mr-2">
                        <img style="height: 50px;width:50px" class="rounded-full" src="{{ url($order->productImage) }}"
                            alt="ProductImg" />

                    </div>
                </div>
            </td>
            <td>{{ $order->orderStatus }}</td>
            <td>{{ $currencySymbol->value }}{{ $order->payableAmount }}</td>
            <td>{{ $currencySymbol->value }}{{ number_format($order->gstAmount, 2, '.', ',') }}</td>
            <td> {{ $currencySymbol->value }}{{ $order->totalPayable }}</td>
        </tr>
        
        <tr>
            <td colspan="7">
                <div class="total-part">
                    <div class="total-left w-85 float-left" align="right">
                        <p>Sub Total</p>
                        <p>Tax ({{$gst->value}}%)</p>
                        <p>Total Payable</p>
                    </div>
                    <div class="total-right w-15 float-left text-bold" align="right">
                        <p>{{ $currencySymbol->value }}{{ $order->payableAmount }}</p>
                        <p>{{ $currencySymbol->value }}{{ number_format($order->gstAmount, 2, '.', ',') }}</p>
                        <p>{{ $currencySymbol->value }}{{ $order->totalPayable }}</p>
                    </div>
                    <div style="clear: both;"></div>
                </div> 
            </td>
        </tr>
    </table>
</div>

<h4>Thanks For Order</h4>
</html>