@extends('layouts.app')
@section('title', __('warehouse.stock_transfer_details'))

		@section('content')
		<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
				<x-breadcrumb :langArray="[
                                            'warehouse.warehouse',
                                            'warehouse.stock_transfer_list',
                                            'warehouse.stock_transfer_details',
										]"/>
				<div class="row">
					<div class="col-12 col-lg-12">

                        @include('layouts.session')

                        <input type="hidden" id="base_url" value="{{ url('/') }}">

                        <div class="card">
                    <div class="card-body">
                        <div class="toolbar hidden-print">
                                <div class="text-end">
                                    @can(['stock_transfer.edit'])
                                    <a href="{{ route('stock_transfer.edit', ['id' => $transfer->id]) }}" class="btn btn-outline-primary"><i class="bx bx-edit"></i>{{ __('app.edit') }}</a>
                                    @endcan
                                    <a href="{{ route('stock_transfer.print', ['id' => $transfer->id]) }}" target="_blank" class="btn btn-outline-secondary px-4"><i class="bx bx-printer mr-1"></i>{{ __("app.print") }}</a>

                                    <a href="{{ route('stock_transfer.pdf', ['id' => $transfer->id]) }}" target="_blank" class="btn btn-outline-danger px-4"><i class="bx bxs-file-pdf mr-1"></i>{{ __("app.pdf") }}</a>

                                </div>
                                <hr/>
                            </div>
                        <div id="printForm">
                            <div class="invoice overflow-auto">
                                <div class="min-width-600">
                                    <header>
                                        <div class="row">
                                            <div class="col">
                                                <a href="javascript:;">
                                                    <img src={{ "/company/getimage/" . app('company')['colored_logo'] }} width="80" alt="" />
                                                </a>
                                            </div>
                                            <div class="col company-details">
                                                <h2 class="name">
                                                    <a href="javascript:;">
                                                    {{ app('company')['name'] }}
                                                    </a>
                                                </h2>
                                                <div>{{ app('company')['address'] }}</div>
                                            </div>
                                        </div>
                                    </header>
                                    <main>
                                        <div class="row contacts">
                                            <div class="col invoice-details">
                                                <h1 class="invoice-id">#{{ $transfer->transfer_code }}</h1>
                                                <div class="date">{{ __('app.date') }}: {{ $transfer->formatted_transfer_date  }}</div>
                                            </div>
                                        </div>
                                        @php
                                            $isHasBatchItem = ($transfer->itemTransaction->count() > 0) ? true : false;
                                        @endphp
                                        <table id="printInvoice">
                                            <thead>
                                                <tr class="text-uppercase">
                                                    <th>#</th>
                                                    <th class="text-left">{{ __('warehouse.warehouse') }}</th>
                                                    <th class="text-left">{{ __('item.item') }}</th>
                                                    <th class="{{ !app('company')['show_hsn'] ? 'd-none':'' }} text-left">{{ __('item.hsn') }}</th>
                                                    <th class="{{ !app('company')['show_mrp'] ? 'd-none':'' }}">{{ __('item.mrp') }}</th>
                                                    @if($isHasBatchItem)
                                                        <th class="{{ !app('company')['enable_batch_tracking'] ? 'd-none':'' }}">{{ __('item.batch_no') }}</th>
                                                        <th class="{{ !app('company')['enable_mfg_date'] ? 'd-none':'' }}">{{ __('item.mfg_date') }}</th>
                                                        <th class="{{ !app('company')['enable_exp_date'] ? 'd-none':'' }}">{{ __('item.exp_date') }}</th>
                                                        <th class="{{ !app('company')['enable_model'] ? 'd-none':'' }}">{{ __('item.model_no') }}</th>
                                                        <th class="{{ !app('company')['enable_color'] ? 'd-none':'' }}">{{ __('item.color') }}</th>
                                                        <th class="{{ !app('company')['enable_size'] ? 'd-none':'' }}">{{ __('item.size') }}</th>
                                                    @endif
                                                    <th class="text-left">{{ __('app.qty') }}</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $i=1;
                                                @endphp

                                                @foreach($transfer->itemTransaction as $transaction)
                                                <tr>
                                                    <td class="no">{{ $i++ }}</td>
                                                    <td>
                                                       {{ $transaction->itemStockTransfer->fromWarehouse->name }}
                                                       <br>
                                                       <small class="fst-italic">To</small>
                                                       <br>
                                                       {{ $transaction->itemStockTransfer->toWarehouse->name }}
                                                   </td>
                                                    <td class="text-left">
                                                        <h3>
                                                            <!-- Service Name -->
                                                            {{ $transaction->item->name }}
                                                        </h3>
                                                        <small>
                                                            <!-- Description -->
                                                            {{ $transaction->description }}
                                                        </small>
                                                        <small>
                                                            @if ($transaction->itemSerialTransaction->count() > 0)
                                                                <br>{{ $transaction->itemSerialTransaction->pluck('itemSerialMaster.serial_code')->implode(', ') }}<br>
                                                            @endif
                                                        </small>
                                                   </td>
                                                   <td class="{{ !app('company')['show_hsn'] ? 'd-none':'' }}">
                                                       {{ $transaction->item->hsn }}
                                                   </td>
                                                   <td class="{{ !app('company')['enable_batch_tracking'] ? 'd-none':'' }}">
                                                       {{ $transaction->batch ? $transaction->batch->itemBatchMaster->batch_no : '' }}
                                                   </td>
                                                   <td class="{{ !app('company')['enable_mfg_date'] ? 'd-none':'' }}">
                                                       {{ $transaction->batch ? $formatDate->toUserDateFormat($transaction->batch->itemBatchMaster->mfg_date) : '' }}
                                                   </td>
                                                   <td class="{{ !app('company')['enable_exp_date'] ? 'd-none':'' }}">
                                                       {{ $transaction->batch ? $formatDate->toUserDateFormat($transaction->batch->itemBatchMaster->exp_date) : '' }}
                                                   </td>
                                                   <td class="{{ !app('company')['enable_model'] ? 'd-none':'' }}">
                                                       {{ $transaction->batch ? $transaction->batch->itemBatchMaster->model_no : ''}}
                                                   </td>
                                                   <td class="{{ !app('company')['show_mrp'] ? 'd-none':'' }}">
                                                       {{ $formatNumber->formatWithPrecision($transaction->batch ? $transaction->batch->itemBatchMaster->mrp : $transaction->mrp) }}
                                                   </td>
                                                   <td class="{{ !app('company')['enable_color'] ? 'd-none':'' }}">
                                                       {{ $transaction->batch ? $transaction->batch->itemBatchMaster->color :'' }}
                                                   </td>
                                                   <td class="{{ !app('company')['enable_size'] ? 'd-none':'' }}">
                                                       {{ $transaction->batch ? $transaction->batch->itemBatchMaster->size : '' }}
                                                   </td>
                                                   <td class="">
                                                        {{ $formatNumber->formatQuantity($transaction->quantity) }}
                                                    </td>



                                                </tr>
                                                @endforeach
                                            </tbody>

                                        </table>
                                        <div class="notices">
                                            <div>{{ __('app.note') }} : </div>
                                            <div class="notice">{{ $transfer->note }}</div>
                                        </div>
                                    </main>

                                </div>
                                <!--DO NOT DELETE THIS div. IT is responsible for showing footer always at the bottom-->
                                <div></div>
                            </div>
                        </div>
                    </div>
                </div>
					</div>
				</div>
				<!--end row-->
			</div>
		</div>

        @include("modals.email.send")
        @include("modals.sms.send")


		@endsection

@section('js')
        <script src="{{ versionedAsset('custom/js/modals/email/send.js') }}"></script>
        <script src="{{ versionedAsset('custom/js/sms/sms.js') }}"></script>
@endsection
