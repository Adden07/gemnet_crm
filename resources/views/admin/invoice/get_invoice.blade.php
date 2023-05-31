<!DOCTYPE html>
<html>
<head>
	<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
	<title>Invoice For </title>
	<link href="//fonts.googleapis.com/css?family=Source+Sans+Pro&display=swap" rel="stylesheet">
	<style>
		*{margin: 0; padding: 0;}
		.btn{
			color: #fff;
			background: #2c94f2;
			padding: 10px 20px;
			display:inline-block;
			text-decoration: none;
			margin: 10px 0 0;
		}
		.btn:hover{background: #1376d0}
		body{font-family: 'Source Sans Pro', sans-serif;font-size: 12px}
		@media print{
			.hidden-print{display: none;}
		}
        @page{
            margin-top: 20px;
        }
		.button {
			background-color: #4A81D4; /* Green */
			border: none;
			color: white;
			padding: 10px 22px;
			text-align: center;
			text-decoration: none;
			display: inline-block;
			font-size: 16px;
			}
	</style>

</head>
<body>
	
	<div style="width: 90%; margin: 0 auto;">
					<div style="text-align:center" class="hidden-print">
						<p style="margin:0;">This is Preview click on the button to generate the file</p>
						<a href="{{ route('admin.accounts.invoices.generate_pdf', ['id'=>$invoice->hashid]) }}" class="button">PDF</a>
					</div>
		<table style="width: 100%;">
			<tr>
				<td style="width: 30%;">
					<div style="color: #000;padding: 8px 0px;font-weight: bold;font-size: 20px;text-align: left;">
							<img src="" height="100">
							<img src="" height="100">
					</div>
				</td>
				<td style="width: 30%;">
					<div style="margin: 20px 0;text-align: center; color: #000;padding: 8px 0px;font-weight: bold;font-size: 20px;">SALES TAX INVOICE</div>
				</td>
				<td style="width: 40%;">
					<div style="margin-top: 30px; margin-bottom: 20px;">
						<table style="margin-top: 1px; width: 90%;border-collapse: collapse;">
							<tr>
								<th style="text-align: left;">Invoice #:</th>
								<td style="text-align: left;border-bottom: 1px solid #000;padding: 5px;">{{ $invoice->invoice_id }}</td>
							</tr>
							<tr>
								<th style="text-align: left;">INVOICE DATE:</th>
								<td style="text-align: left;border-bottom: 1px solid #000;padding: 5px;">{{ date('d-M-Y', strtotime($invoice->created_at)) }}</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
		</table>

		<table style="width: 100%;">
			<tr>
				<td style="width: 50%; vertical-align: top">
					<table style="width: 98%; border-collapse: collapse;border: 1px solid #000;">
						<tr>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;border-right: 1px solid #000;font-weight: bold;width: 100px">Customer Name:</td>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;">
								{{ $invoice->user->name }}
							</td>
						</tr>

						<tr>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;border-right: 1px solid #000;    font-weight: bold;">Customer ID:</td>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;">
								{{ $invoice->user->c_id }}
							</td>
						</tr>
						<tr>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;border-right: 1px solid #000;font-weight: bold;">User Name:</td>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;">
								{{ $invoice->user->username }}
							</td>
						</tr>
						<tr>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;border-right: 1px solid #000;font-weight: bold;">Address:</td>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;">
								{{ $invoice->user->address }}
							</td>
						</tr>

						<tr>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;border-right: 1px solid #000;font-weight: bold;">Email:</td>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;">{{ $invoice->user->email }}</td>
						</tr>
						<tr>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;border-right: 1px solid #000;font-weight: bold;">Cell or Phone #</td>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;">{{ $invoice->user->mobile }}</td>
						</tr>
						<tr>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;border-right: 1px solid #000;font-weight: bold;">CNIC or NTN:</td>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;">
								{{ (!is_null($invoice->user->ntn)) ? $invoice->user->ntn : $invoice->user->nic }}
							</td>
						</tr>
					</table>
				</td>
				<td style="width: 50%;">
					<table style="width: 100%; border-collapse: collapse;border: 1px solid #000;margin-left: 12px;">
						<tr>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;border-right: 1px solid #000;font-weight: bold;width: 100px">Supplier Name:</td>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;">{{ @$edit_setting->company_name }}</td>
						</tr>

						<tr>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;border-right: 1px solid #000;font-weight: bold;">Address:</td>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;">{{ @$edit_setting->address }}</td>
						</tr>

						<tr>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;border-right: 1px solid #000;font-weight: bold;">Web:</td>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;">www.ges.net.pk</td>
						</tr>

						<tr>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;border-right: 1px solid #000;font-weight: bold;">Phone #:</td>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;">{{ @$edit_setting->mobile }}</td>
						</tr>
						<tr>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;border-right: 1px solid #000;font-weight: bold;">Email:</td>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;">{{ @$edit_setting->email }}</td>
						</tr>
						<tr>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;border-right: 1px solid #000;font-weight: bold;">NTN #:</td>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;">{{ @$edit_setting->ntn }}</td>
						</tr>

						<tr>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;border-right: 1px solid #000;font-weight: bold;">SRB Sales Tax #:</td>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;">{{ @$edit_setting->srb_sales_tax }}</td>
						</tr>
					</table>

				</td>
			</tr>
		</table>

		<table style="width: 100%;">
			<tr>
				<td style="width: 100%;">
					<div style="width: 100%;margin-top: 20px;margin-bottom: 0px;">
						<table style="width: 100%; border-collapse: collapse;border: 1px solid #000;">
							<tr style="background: #777777;color: white;">
								<th style="padding: 5px; border-bottom: 1px solid #000;text-align: center;border-right: 1px solid #000;">Service Description</th>
								<th style="padding: 5px; border-bottom: 1px solid #000;text-align: center;border-right: 1px solid #000;">Amount</th>
								<th style="padding: 5px; border-bottom: 1px solid #000;text-align: center;border-right: 1px solid #000;">Sales Tax</th>
								<th style="padding: 5px; border-bottom: 1px solid #000;text-align: center;border-right: 1px solid #000;">Advance I.T.</th>
							</tr>

							<tr>
								<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;border-right: 1px solid #000;">MRC Internet - {{ $invoice->package->name }}</td>
								<td style="padding: 5px; border-bottom: 1px solid #000;text-align: right;border-right: 1px solid #000;">PKR {{ number_format($invoice->pkg_price) }}</td>
								<td style="padding: 5px; border-bottom: 1px solid #000;text-align: right;border-right: 1px solid #000;">PKR {{ number_format($invoice->sales_tax) }}</td>
								<td style="padding: 5px; border-bottom: 1px solid #000;text-align: right;border-right: 1px solid #000;">PKR {{ number_format($invoice->adv_inc_tax) }}</td>
							</tr>


						</table>
					</div>	
				</td>
			</tr>
		</table>

		<table style="width: 100%;">
			<tr>
				<td style="width: 100%;">
					<div style="width: 100%;margin-top: 15px;margin-bottom: 0px;">
						<table style="width: 100%; border-collapse: collapse;border: 1px solid #000;">
							<tr style="background: #777777;color: white;">
								<th style="padding: 5px; border-bottom: 1px solid #000;text-align: center;border-right: 1px solid #000;">GROSS TOTAL (Including Taxes)</th>
								<th  style="padding: 5px; border-bottom: 1px solid #000;text-align: center;">PKR {{ number_format($invoice->total) }}</th>
								<th colspan="3" style="padding: 5px; border-bottom: 1px solid #000;text-align: right"></th>
							</tr>
							<tr>
								<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;border-right: 1px solid #000;">Discount</td>
								<td style="padding: 5px; border-bottom: 1px solid #000;text-align: right;">PKR {{ number_format($invoice->discount) }}</td>
								<td style="padding: 5px; border-bottom: 1px solid #000;text-align: right;"></td>
								<td style="padding: 5px; border-bottom: 1px solid #000;text-align: right;"></td>
								<td style="padding: 5px; border-bottom: 1px solid #000;text-align: right;border-right: 1px solid #000;"></td>
							</tr>
							<tr>
							<!--Arrears (unpaid amount or tax challans) !-->
								<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;border-right: 1px solid #000;">Balance / (Advance)</td>
								<td style="padding: 5px; border-bottom: 1px solid #000;text-align: right;">PKR {{ number_format($invoice->user->user_current_balance) }}</td>
								<td style="padding: 5px; border-bottom: 1px solid #000;text-align: right;"></td>
								<td style="padding: 5px; border-bottom: 1px solid #000;text-align: right;"></td>
								<td style="padding: 5px; border-bottom: 1px solid #000;text-align: right;border-right: 1px solid #000;">
								</td>
							</tr>

						</div>
					</td>
				</tr>
			</table>
		</tr>
	</table>

<!--Receivable / (Advance Balance) !-->

<table style="width: 100%;">
	<tr>
		<td style="width: 40%;"></td>
		<td style="width: 90%;">
			<div style="width: 100%;margin-top: 20px;">
				<table style="width: 100%; border-collapse: collapse;border: 1px solid #000;">
					<tr style="color: white;">
						<th style="padding: 5px; border-bottom: 1px solid #000;text-align: center;border-right: 1px solid #000;width: 65%;background: #777777;"> Receivable / (Advance Balance)	
						</th>
						<th style="padding: 5px; border-bottom: 1px solid #000;text-align: right;border-right: 1px solid #000;color: #000;">
						PKR {{ number_format($invoice->total) }}
						</th>
					</tr>
				</table>
			</div>
		</td>
	</tr>
</table>
	
<table style="width: 100%;">
	<tr>
		<td style="width: 100%;">
			<div style="width: 100%;">
				<h4 style="margin-bottom: 10px;">Amount in Words: <span>Rupees Only {{ convertNumberToWord($invoice->total) }}</span></h4>
			</div>
		</td>
	</tr>
</table>

<table style="width: 100%;">
	<tr>
		<td style="vertical-align: top;">
			<table style="width: 100%; border-collapse: collapse;border: 1px solid #000;margin-left:12px;">
				<tr style="background:#777777;color: white;">
					<th style="padding: 5px; border: 1px solid #000;text-align: center;width: 100%;" colspan="2">Payment History (Last 3 Months)</th>
				</tr>
				<tr style="background: #cccccc;">
					<th style="padding: 5px; border-bottom: 1px solid #000;text-align: center;border-right: 1px solid #000;">Payment Date</th>
					<th style="padding: 5px; border-bottom: 1px solid #000;text-align: center;border-right: 1px solid #000;">Amount</th>
				</tr>
				@foreach($past_invoices AS $inv)
	            <tr style="">
					
					<th style="padding: 5px; border-bottom: 1px solid #000;text-align: center;border-right: 1px solid #000;">{{ date('d-M-Y', strtotime($inv->created_at)) }}</th>
					<th style="padding: 5px; border-bottom: 1px solid #000;text-align: right;border-right: 1px solid #000;">
						PKR {{ number_format($inv->total) }}
					</th>
				
				</tr>
				@endforeach
				<tr></tr>
			</table>
		</td>
	</tr>
</table>




<h4 style="text-align: center;line-height: 36px;font-size: 15px;margin-top: 5px; margin-top: -10px; margin-bottom: 10px;">This is a system generated invoice and does not require any signatures</h4>

    <hr style="margin-left: 20px;border-top: 2px dotted #000;">
</div>
<h2 style="margin-top: 10px">Bank Copy</h2>

<table style="width: 100%;">
			<tr>
				<td style="width: 50%; vertical-align: top">
					<table style="width: 98%; border-collapse: collapse;border: 0px">
						<tr>
							<td style="padding: 5px; border: 1px solid #000;text-align: left;font-weight: bold;width: 150px">Bank Name:</td>
							<td style="padding: 5px; border: 1px solid #000;text-align: left;">
								{{ @$edit_setting->bank_name }}
							</td>
						</tr>

						<tr>
							<td style="padding: 5px; border: 1px solid #000;text-align: left;font-weight: bold;">Account Title:</td>
							<td style="padding: 5px; border: 1px solid #000;text-align: left;">
								{{ @$edit_setting->account_title }}
							</td>
						</tr>
						<tr>
							<td style="padding: 5px; border: 1px solid #000;text-align: left;font-weight: bold;">Account Number #</td>
							<td style="padding: 5px; border: 1px solid #000;text-align: left;">
								{{ @$edit_setting->account_no }}
							</td>
						</tr>

                        <tr>
							<td colspan="2" style="padding: 5px; border: 0px;text-align: left;font-weight: bold;font-size: 14px"> Payment For Gemnet Enterprise Solutions Pvt Ltd.</td>
						</tr>

                       

					</table>
				</td>

				<td style="width: 50%;">
					<table style="width: 100%; border-collapse: collapse;border: 1px solid #000;margin-left: 12px;">
						<tr>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;border-right: 1px solid #000;font-weight: bold;width: 100px">Customer ID: </td>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;">{{ $invoice->user->c_id }}</td>
						</tr>

						<tr>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;border-right: 1px solid #000;font-weight: bold;">Customer Name: </td>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;">{{ $invoice->user->name }}</td>
						</tr>

						<tr>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;border-right: 1px solid #000;font-weight: bold;">CNIC:</td>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;">{{ $invoice->user->nic }}</td>
						</tr>

						<tr>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;border-right: 1px solid #000;font-weight: bold;">Mobile No:</td>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;">{{ $invoice->user->mobile }}</td>
						</tr>
						<tr>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;border-right: 1px solid #000;font-weight: bold;">Amount:</td>
							<td style="padding: 5px; border-bottom: 1px solid #000;text-align: left;">{{ (($invoice->user->user_current_balance < 0)) ? number_format($invoice->user_current_balance) : 0 }}</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>

		<table style="width: 100%;">
			<tr>
				<td style="width: 100%;">
					<div style="width: 100%;">
						<h4 style="margin-bottom: 10px;">Amount in Words: <span>Rupees Only</span></h4>
					</div>
				</td>
			</tr>
		</table>
<table style="margin: 10px 0; width: 100%;" >
<th style="padding: 20px 0;">
	<td style=" font-size: 16px; font-weight: bold;">Depositer's Name:_____________</td>
	<td style=" font-size: 16px; font-weight: bold;">Mobile:________________</td>
	<td style=" font-size: 16px; font-weight: bold;">NIC:_________________</td>
</th>

</table>

<table style="width: 100%;">
	<tr>
		<td style="width: 100%;">
			<div style="width:100%;margin-top: 50px;">
            <br>
            <br>
		<h3 style="margin-top: 35px; text-align:center"><u>TERMS & CONDITIONS</u></h3>
		<p style="margin-top: 10px;margin-bottom: 5px;font-size: 14px;font-weight: bold;">1. All payments shall be due on the date indicated on this invoice. Any balance amount remaining unpaid on the next invoicing date, shall be considered in default and will be subject to a surcharge of five percent (5%) of the unpaid balance due per month.</p>
		<p style="margin-top: 10px;margin-bottom: 5px;font-size: 14px;font-weight: bold;">2. If the Customer does not pay any charges owed to GES when due or violates any of the terms of the Agreement, then GES will have the right to discontinue or restrict the Service either temporarily or permanently without any notice to the Customer.</p>
		<p style="margin-top: 10px;margin-bottom: 5px;font-size: 14px;font-weight: bold;">3. Payment can be made through Cross Cheque/ Pay Order in the name of Gemnet Enterprise Solutions (Pvt) Ltd.</p>
		<p style="margin-top: 10px;margin-bottom: 5px;font-size: 14px;font-weight: bold;">4. Please ignore arrears, if payment is already made.</p>
		<p style="margin-top: 10px;margin-bottom: 5px;font-size: 14px;font-weight: bold;">5. Advance tax @ 12.5% is collectable on the internet services under section 236 (1) (d) of the Income Tax Ordinance, 2001.</p>
		<h5 style="text-align: center;border-top: 1px solid #000;line-height: 36px;font-size: 15px;margin-top: 35px;">For more inquiries, please call our Customer Services Department at # 022-4177777  Ext: 1155  or email us at billing@ges.net.pk</h5>
	</div>
		</td>
	</tr>
</table>
	</div>
</body>
</html>