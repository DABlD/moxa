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
		<td>
			@if($dataset['type'] == "consumption")
				Amount
			@endif
		</td>

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

	@php
		$dataset['data'] = array_values($dataset['data']);
		$dataset['values'] = array_values($dataset['values']);
	@endphp
	@for($key = 1; $key < sizeof($dataset['data']); $key++)
		@php
			if(isset($dataset['values'][$key])){
				$consumption = $dataset['values'][$key]['payload'] - $dataset['values'][$key-1]['payload'];
			}
			else{
				$consumption = 0;
			}

			if($consumption < 0){
				break;
			}
		@endphp

		<tr>
			<td>{{ $key }}</td>
			<td>{{ isset($dataset['values'][$key-1]) ? $dataset['values'][$key-1]['created_at'] : "-" }}</td>
			<td>{{ isset($dataset['values'][$key-1]) ? $dataset['values'][$key-1]['date'] : "-" }}</td>
			<td>{{ isset($dataset['values'][$key-1]) ? $dataset['values'][$key-1]['payload'] : "-" }}</td>
			<td>{{ isset($dataset['values'][$key]) ? $dataset['values'][$key]['date'] : "-" }}</td>
			<td>{{ isset($dataset['values'][$key]) ? $dataset['values'][$key]['payload'] : "-" }}</td>
			<td>{{ $consumption > 0 ? $consumption : false }}</td>
			<td>
				@if($dataset['type'] == "consumption")
					{{ $consumption > 0 ? $consumption * $dataset['rate'] : false }}
				@endif
			</td>

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
			<td>{{ $dataset['data'][$key-1] != "" ? $dataset['data'][$key-1] : "-" }}</td>
		</tr>
	@endfor
</table>