<div class="activity__detail">
    @if ($incoming)
        <div>
        </div>
        <div class="activity__element__list">
            <h3>
                @lang('lite/title.incoming_funds')
                <a href="{{ route('lite.activity.transaction.edit', [$activity->id, 1]) }}"
                   class="edit-activity" title="Edit">@lang('lite/elementForm.edit_incoming_funds')
                </a>
            </h3>
            <div class="activity__element--info">
                <ul>
                    @foreach ($incoming as $index => $transaction)
                        <li>
                        <span>
                               {{ getVal($transaction, ['transaction', 'value', 0, 'amount']) }}
                            @if(getVal($transaction, ['transaction', 'value', 0, 'currency']))
                                {{ getVal($transaction, ['transaction', 'value', 0, 'currency']) }}
                            @else
                                {{ $defaultCurrency }}
                            @endif
                            @if(getVal($transaction, ['transaction', 'value', 0, 'date']))
                                [{{ getVal($transaction, ['transaction', 'value', 0, 'date']) }}]
                            @endif
                            <a data-href="{{ route('lite.activity.transaction.delete', $activity->id) }}"
                               data-index="{{ getVal($transaction, ['id'], '') }}"
                               class="delete-activity delete-confirm" data-toggle="modal" data-target="#delete-modal"
                               data-message="@lang('lite/global.confirm_delete')"> @lang('lite/global.delete') </a>
                    </span>
                        </li>
                    @endforeach
                </ul>
            </div>
            <a href="{{ route('lite.activity.transaction.create', [$activity->id, 1]) }}"
               class="add-more"><span>@lang('lite/elementForm.add_incoming_funds')</span></a>
        </div>
    @else
        <div class="activity__element__list">
            <a href="{{ route('lite.activity.transaction.create', [$activity->id, 1]) }}"
               class="add-more"><span>@lang('lite/elementForm.add_incoming_funds')</span></a>
        </div>
    @endif
</div>
