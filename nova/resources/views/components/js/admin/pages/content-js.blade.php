<script>
	var vm = new Vue({
		el: "#app",
		data: {
			loading: true,
			loadingWithError: false,
			baseUrl: "{{ Request::root() }}",
			key: "",
			value: "",
			contents: []
		},

		methods: {
			removeContent: function(contentId)
			{
				$('#removeContent').modal({
					remote: "{{ url('admin/content') }}/" + contentId + "/remove"
				}).modal('show');
			},

			resetFilters: function()
			{
				this.key = "";
				this.value = "";
			}
		},

		ready: function()
		{
			this.$http.get(this.baseUrl + '/api/page-contents', function (data, status, request)
			{
				this.contents = data.data;
			}).error(function (data, status, request)
			{
				this.loadingWithError = true;
			});
		}
	});

	vm.$watch('contents', function (newValue, oldValue)
	{
		if (newValue.length > 0)
			this.loading = false;
	});
</script>