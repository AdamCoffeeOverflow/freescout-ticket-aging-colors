@if (Auth::user()->can('update', $mailbox))
    <li @if (Route::currentRouteName() == 'adamticketagingcolors.mailboxes.settings')class="active"@endif>
        <a href="{{ route('adamticketagingcolors.mailboxes.settings', ['id' => $mailbox->id]) }}">
            <i class="glyphicon glyphicon-time"></i> {{ __('Ticket Aging Colors') }}
        </a>
    </li>
@endif
