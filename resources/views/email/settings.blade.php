@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>Email Notification Settings</h2>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('email.settings.update') }}">
                        @csrf

                        <div class="form-group mb-4">
                            <div class="form-check">
                                <input type="checkbox"
                                       class="form-check-input"
                                       id="email_notifications_enabled"
                                       name="email_notifications_enabled"
                                       {{ auth()->user()->email_notifications_enabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_notifications_enabled">
                                    <strong>Enable email notifications</strong>
                                </label>
                                <small class="form-text text-muted d-block">
                                    Receive email notifications for important events.
                                </small>
                            </div>
                        </div>

                        <hr>

                        <h5 class="mb-3">Notification Types</h5>

                        @php
                            $preferences = auth()->user()->notification_preferences;
                        @endphp

                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input type="checkbox"
                                       class="form-check-input"
                                       id="note_shared"
                                       name="preferences[note_shared]"
                                       {{ $preferences['note_shared'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="note_shared">
                                    Notes shared with me
                                </label>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input type="checkbox"
                                       class="form-check-input"
                                       id="collaborator_invitation"
                                       name="preferences[collaborator_invitation]"
                                       {{ $preferences['collaborator_invitation'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="collaborator_invitation">
                                    Collaboration invitations
                                </label>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input type="checkbox"
                                       class="form-check-input"
                                       id="note_updated"
                                       name="preferences[note_updated]"
                                       {{ $preferences['note_updated'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="note_updated">
                                    Notes I collaborate on are updated
                                </label>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input type="checkbox"
                                       class="form-check-input"
                                       id="note_commented"
                                       name="preferences[note_commented]"
                                       {{ $preferences['note_commented'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="note_commented">
                                    New comments on my notes
                                </label>
                            </div>
                        </div>

                        <hr>

                        <h5 class="mb-3">Email Digests</h5>

                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input type="checkbox"
                                       class="form-check-input"
                                       id="daily_digest"
                                       name="preferences[daily_digest]"
                                       {{ $preferences['daily_digest'] ?? false ? 'checked' : '' }}>
                                <label class="form-check-label" for="daily_digest">
                                    Daily digest email
                                </label>
                                <small class="form-text text-muted d-block">
                                    Receive a daily summary of your activity.
                                </small>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <div class="form-check">
                                <input type="checkbox"
                                       class="form-check-input"
                                       id="weekly_summary"
                                       name="preferences[weekly_summary]"
                                       {{ $preferences['weekly_summary'] ?? false ? 'checked' : '' }}>
                                <label class="form-check-label" for="weekly_summary">
                                    Weekly summary email
                                </label>
                                <small class="form-text text-muted d-block">
                                    Receive a weekly summary every Monday.
                                </small>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                Save Settings
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="alert alert-info">
                        <h6>Need to unsubscribe from all emails?</h6>
                        <p class="mb-2">Use this link to unsubscribe from all email notifications:</p>
                        <a href="{{ auth()->user()->getUnsubscribeUrl() }}" class="btn btn-sm btn-outline-danger">
                            Unsubscribe from all emails
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
