@extends('layouts.app')

@section('title_full', __('Ticket Aging Colors').' - '.$mailbox->name)

@section('sidebar')
  @include('partials/sidebar_menu_toggle')
  @include('mailboxes/sidebar_menu')
@endsection

@section('content')

  <div class="section-heading">
    {{ __('Ticket Aging Colors') }}
  </div>

  @include('partials/flash_messages')

  <div class="row-container adamtac-settings">
    <div class="row">
      <div class="col-xs-12">

        <form class="form-horizontal margin-top" method="POST" action="{{ route('adamticketagingcolors.mailboxes.settings.save', ['id' => $mailbox->id]) }}">
          {{ csrf_field() }}

          <div class="form-group">
            <label for="enabled" class="col-sm-2 control-label">{{ __('Enabled') }}</label>
            <div class="col-sm-6">
              <div class="controls">
                <div class="onoffswitch-wrap">
                  <div class="onoffswitch">
                    <input type="checkbox" name="enabled" value="1" id="enabled" class="onoffswitch-checkbox" @if (!empty($settings['enabled']))checked="checked"@endif>
                    <label class="onoffswitch-label" for="enabled"></label>
                  </div>
                  <i class="glyphicon glyphicon-info-sign icon-info icon-info-inline" data-toggle="popover" data-trigger="hover" data-html="true" data-placement="left" data-title="{{ __('Ticket Aging Colors') }}" data-content="{{ __('Applies only to Active tickets and resets automatically when the ticket status changes. Closed tickets are never color-coded.') }}"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="baseline" class="col-sm-2 control-label">{{ __('Start counting from') }}</label>
            <div class="col-sm-6">
              <select name="baseline" id="baseline" class="form-control input-sized">
                <option value="status_change" @if (($settings['baseline'] ?? '') === 'status_change')selected="selected"@endif>{{ __('Last status change (recommended)') }}</option>
                <option value="waiting_since" @if (($settings['baseline'] ?? '') === 'waiting_since')selected="selected"@endif>{{ __('Waiting since (folder setting)') }}</option>              </select>
              <p class="block-help">
                {{ __('Waiting since uses the folder\'s waiting-since timestamp when available. If the folder has no waiting-since field, rows won\'t be color-coded for that baseline.') }}
              </p>
            </div>
          </div>

          
          <div class="form-group">
            <label class="col-sm-2 control-label">{{ __('Rules') }}</label>
            <div class="col-sm-10">
              <p class="block-help">
                {{ __('Configure up to three escalation levels. Each level applies when the selected time threshold is exceeded.') }}
              </p>
            <p class="text-muted">
              {{ __('Row colors are fixed to a built-in palette for maximum theme compatibility. ') }}
            </p>

              <div class="row">
                <div class="col-sm-4">
                  <h4 class="margin-top-0">{{ __('Level 1') }}</h4>

                  <label>{{ __('Threshold') }}</label>
                  <div class="row">
                    <div class="col-xs-6">
                      <input type="number" min="0" class="form-control"
                          name="yellow_value"
                          value="{{ (int)($settings['yellow_value'] ?? ($settings['yellow_days'] ?? 2)) }}">
                    </div>
                    <div class="col-xs-6">
                      <select name="yellow_unit" class="form-control">
                        @php $u = $settings['yellow_unit'] ?? 'business_days'; @endphp
                        <option value="minutes" @if($u==='minutes')selected="selected"@endif>{{ __('Minutes') }}</option>
                        <option value="hours" @if($u==='hours')selected="selected"@endif>{{ __('Hours') }}</option>
                        <option value="calendar_days" @if($u==='calendar_days')selected="selected"@endif>{{ __('Calendar days') }}</option>
                        <option value="business_days" @if($u==='business_days')selected="selected"@endif>{{ __('Business days') }}</option>
                      </select>
                    </div>
                  </div>

                  <label class="margin-top">{{ __('Color Intensity') }}</label>
                  <div class="row">
                    <div class="col-xs-12">
                      <input type="range" min="5" max="100" step="5"
                          name="yellow_intensity"
                          id="yellow_intensity"
                          value="{{ (int)($settings['yellow_intensity'] ?? 20) }}"
                          class="form-control-range">
                      <small class="text-muted">
                        <span id="yellow_intensity_val">{{ (int)($settings['yellow_intensity'] ?? 20) }}</span>%
                      </small>
                    </div>
                  </div>
</div>

                <div class="col-sm-4">
                  <h4 class="margin-top-0">{{ __('Level 2') }}</h4>

                  <label>{{ __('Threshold') }}</label>
                  <div class="row">
                    <div class="col-xs-6">
                      <input type="number" min="0" class="form-control"
                          name="orange_value"
                          value="{{ (int)($settings['orange_value'] ?? ($settings['red_value'] ?? ($settings['red_days'] ?? 4))) }}">
                    </div>
                    <div class="col-xs-6">
                      <select name="orange_unit" class="form-control">
                        @php $u = $settings['orange_unit'] ?? ($settings['red_unit'] ?? 'business_days'); @endphp
                        <option value="minutes" @if($u==='minutes')selected="selected"@endif>{{ __('Minutes') }}</option>
                        <option value="hours" @if($u==='hours')selected="selected"@endif>{{ __('Hours') }}</option>
                        <option value="calendar_days" @if($u==='calendar_days')selected="selected"@endif>{{ __('Calendar days') }}</option>
                        <option value="business_days" @if($u==='business_days')selected="selected"@endif>{{ __('Business days') }}</option>
                      </select>
                    </div>
                  </div>

                  <label class="margin-top">{{ __('Color Intensity') }}</label>
                  <div class="row">
                    <div class="col-xs-12">
                      <input type="range" min="5" max="100" step="5"
                          name="orange_intensity"
                          id="orange_intensity"
                          value="{{ (int)($settings['orange_intensity'] ?? ($settings['red_intensity'] ?? 25)) }}"
                          class="form-control-range">
                      <small class="text-muted">
                        <span id="orange_intensity_val">{{ (int)($settings['orange_intensity'] ?? ($settings['red_intensity'] ?? 25)) }}</span>%
                      </small>
                    </div>
                  </div>
</div>

                <div class="col-sm-4">
                  <h4 class="margin-top-0">{{ __('Level 3') }}</h4>

                  <label>{{ __('Threshold') }}</label>
                  <div class="row">
                    <div class="col-xs-6">
                      <input type="number" min="0" class="form-control"
                          name="red_value"
                          value="{{ (int)($settings['red_value'] ?? ($settings['deep_red_value'] ?? ($settings['deep_red_days'] ?? 6))) }}">
                    </div>
                    <div class="col-xs-6">
                      <select name="red_unit" class="form-control">
                        @php $u = $settings['red_unit'] ?? ($settings['deep_red_unit'] ?? 'business_days'); @endphp
                        <option value="minutes" @if($u==='minutes')selected="selected"@endif>{{ __('Minutes') }}</option>
                        <option value="hours" @if($u==='hours')selected="selected"@endif>{{ __('Hours') }}</option>
                        <option value="calendar_days" @if($u==='calendar_days')selected="selected"@endif>{{ __('Calendar days') }}</option>
                        <option value="business_days" @if($u==='business_days')selected="selected"@endif>{{ __('Business days') }}</option>
                      </select>
                    </div>
                  </div>

                  <label class="margin-top">{{ __('Color Intensity') }}</label>
                  <div class="row">
                    <div class="col-xs-12">
                      <input type="range" min="5" max="100" step="5"
                          name="red_intensity"
                          id="red_intensity"
                          value="{{ (int)($settings['red_intensity'] ?? ($settings['deep_red_intensity'] ?? 30)) }}"
                          class="form-control-range">
                      <small class="text-muted">
                        <span id="red_intensity_val">{{ (int)($settings['red_intensity'] ?? ($settings['deep_red_intensity'] ?? 30)) }}</span>%
                      </small>
                    </div>
                  </div>
</div>
              </div>
            </div>
          </div>
</div>

          <div class="form-group">
            <div class="col-sm-6 col-sm-offset-2">
              <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
</div>
          </div>
</form>

      </div>
    </div>
  </div>

@endsection
