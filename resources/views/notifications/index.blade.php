@extends('layouts.app')

@section('title', 'Notifications')

@section('content')

<div class="space-y-6">

    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Notifications
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Maintenance alerts and system notifications.
            </p>
        </div>

        @if(auth()->user()->unreadNotifications->count())

            <form
                method="POST"
                action="{{ route('notifications.read-all') }}"
            >
                @csrf

                <button
                    type="submit"
                    class="text-sm font-medium text-gray-600 hover:text-gray-900"
                >
                    Mark all as read
                </button>

            </form>

        @endif

    </div>


    <div class="overflow-hidden rounded-xl bg-white shadow-sm">

        @forelse($notifications as $notification)

            <a
                href="{{ route(
                    'notifications.read',
                    $notification->id
                ) }}"
                class="block border-b border-gray-100 p-5 hover:bg-gray-50
                {{ $notification->read_at
                    ? ''
                    : 'bg-blue-50'
                }}"
            >

                <div class="flex gap-4">

                    <div class="text-xl">
                        @if(
                            $notification->data['reason']
                            === 'overdue'
                        )
                            🔴
                        @elseif(
                            $notification->data['reason']
                            === 'due_soon'
                        )
                            🟡
                        @else
                            🚗
                        @endif
                    </div>


                    <div class="flex-1">

                        <p class="font-semibold text-gray-900">
                            {{ $notification->data['title'] }}
                        </p>

                        <p class="mt-1 text-sm text-gray-600">
                            {{ $notification->data['message'] }}
                        </p>

                        <p class="mt-2 text-xs text-gray-400">
                            {{ $notification->created_at->diffForHumans() }}
                        </p>

                    </div>

                </div>

            </a>

        @empty

            <div class="p-10 text-center text-sm text-gray-500">
                No notifications.
            </div>

        @endforelse

    </div>


    <div>
        {{ $notifications->links() }}
    </div>

</div>

@endsection