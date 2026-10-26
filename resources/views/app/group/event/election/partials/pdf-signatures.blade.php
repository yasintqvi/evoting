<table style="width:100%; margin-top:40px; border-collapse:collapse;">
    <tr>
        @foreach (['رئیس جلسه', 'ناظر اول', 'ناظر دوم', 'منشی'] as $role)
            <td style="width:25%; text-align:center; padding:8px; vertical-align:top;">
                <strong>{{ $role }}:</strong>
                <div style="height:50px;"></div>
            </td>
        @endforeach
    </tr>
</table>
