<?php

namespace App\DTOs;

use Carbon\Carbon;
use InvalidArgumentException;

class IclockAttendanceDTO
{
    public function __construct(
        public readonly string $employeeId,
        public readonly Carbon $attendanceAt,
        public readonly int $status,
        public readonly int $punch,
        public readonly ?string $workCode = null,
        public readonly ?int $reserved1 = null,
        public readonly ?int $reserved2 = null,
        public readonly array $rawColumns = [],
    ) {
    }

    public static function fromRaw(string $raw): self
    {
        $columns = explode("\t", trim($raw));

        return self::fromArray($columns);
    }

    public static function fromArray(array $columns): self
    {
        if (count($columns) < 4) {
            throw new InvalidArgumentException('Invalid attendance payload: minimum 4 columns required.');
        }

        $employeeId = trim((string) ($columns[0] ?? ''));
        $timestamp  = trim((string) ($columns[1] ?? ''));
        $status     = trim((string) ($columns[2] ?? '0'));
        $punch      = trim((string) ($columns[3] ?? '0'));
        $workCode   = isset($columns[4]) && $columns[4] !== '' ? trim((string) $columns[4]) : null;
        $reserved1  = isset($columns[5]) && $columns[5] !== '' ? (int) $columns[5] : null;
        $reserved2  = isset($columns[6]) && $columns[6] !== '' ? (int) $columns[6] : null;

        if ($employeeId === '') {
            throw new InvalidArgumentException('Invalid attendance payload: employee_id is missing.');
        }

        if ($timestamp === '') {
            throw new InvalidArgumentException('Invalid attendance payload: timestamp is missing.');
        }

        try {
            $attendanceAt = Carbon::createFromFormat('Y-m-d H:i:s', $timestamp);
        } catch (\Throwable $e) {
            throw new InvalidArgumentException("Invalid attendance timestamp: {$timestamp}");
        }

        if (!is_numeric($status) || !is_numeric($punch)) {
            throw new InvalidArgumentException('Invalid attendance payload: status or punch is not numeric.');
        }

        return new self(
            employeeId: $employeeId,
            attendanceAt: $attendanceAt,
            status: (int) $status,
            punch: (int) $punch,
            workCode: $workCode,
            reserved1: $reserved1,
            reserved2: $reserved2,
            rawColumns: $columns,
        );
    }

    public static function isSkippablePayload(string $raw): bool
    {
        $raw = trim($raw);

        if ($raw === '') {
            return true;
        }

        // Device sometimes sends non-attendance lines
        return str_starts_with($raw, 'USER')
            || str_starts_with($raw, 'FP')
            || str_starts_with($raw, 'FACE')
            || str_starts_with($raw, 'OPLOG')
            || str_starts_with($raw, 'C:');
    }

    public function toArray(): array
    {
        return [
            'employee_id' => $this->employeeId,
            'attendance_at' => $this->attendanceAt->toDateTimeString(),
            'status' => $this->status,
            'punch' => $this->punch,
            'work_code' => $this->workCode,
            'reserved1' => $this->reserved1,
            'reserved2' => $this->reserved2,
            'raw_columns' => $this->rawColumns,
        ];
    }
}