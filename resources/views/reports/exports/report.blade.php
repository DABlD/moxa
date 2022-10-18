@php
	$bold = "font-weight: bold;";
	$center = "text-align: center;";
@endphp

<table>

	<tr><td></td></tr>
	<tr><td></td></tr>
	<tr><td></td></tr>
	<tr><td></td></tr>
	<tr><td></td></tr>
	<tr><td></td></tr>
	<tr><td></td></tr>
	<tr><td></td></tr>
	<tr><td></td></tr>
	<tr><td></td></tr>
	<tr><td></td></tr>
	<tr><td></td></tr>
	<tr><td></td></tr>
	<tr><td></td></tr>
	<tr><td></td></tr>
	<tr><td></td></tr>
	<tr><td></td></tr>
	<tr><td></td></tr>
	<tr><td></td></tr>
	<tr><td></td></tr>

	<tr>
		<td>#</td>
		<td>Reading Date</td>
		<td>Start</td>
		<td>Start Reading</td>
		<td>End</td>
		<td>End Reading</td>
		<td>Consumption</td>

		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td>CONSUMPTION</td>
	</tr>

	@for($key = 1; $key < sizeof($dataset['data']); $key++)
		@php
			$consumption = $dataset['values'][$key]['payload'] - $dataset['values'][$key-1]['payload'];
			if($consumption < 0){
				break;
			}
		@endphp

		<tr>
			<td>{{ $key }}</td>
			<td>{{ $dataset['values'][$key-1]['created_at'] }}</td>
			<td>{{ $dataset['values'][$key-1]['date'] }}</td>
			<td>{{ $dataset['values'][$key-1]['payload'] }}</td>
			<td>{{ $dataset['values'][$key]['date'] }}</td>
			<td>{{ $dataset['values'][$key]['payload'] }}</td>
			<td>{{ $consumption > 0 ? $consumption : false }}</td>

			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>

			<td>{{ $labels[$key-1] }}</td>
			<td>{{ $dataset['data'][$key-1] }}</td>
		</tr>
	@endfor
</table>