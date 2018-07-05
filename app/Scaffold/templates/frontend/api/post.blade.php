export function {{$functionName}}({{join(', ',$parameters)}}) {
  return request({
    // params,
    data,
    url: `{{$route}}`,
    method: 'post'
  })
}