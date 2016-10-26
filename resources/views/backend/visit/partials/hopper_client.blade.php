<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Hopper Desktop Client</h3>
    </div><!-- /.box-header -->
    <div class="box-body">		
		<div v-if="hopperClient" class="alert alert-success">
			<i class="fa fa-signal" aria-hidden="true"></i> Your Hopper Client is <strong>Online</strong>
		</div>
		<div v-else class='alert alert-warning'>
			<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Your Hopper Client is <strong>Offline</strong>
		</div>
		<p>Hopper Desktop Client is an application that sits on your system, connected to Hopper and linked to your account via your login credentials. Hopper client allows you to use the same File Upload tools as above, but use them faster by taking advantage of native File API tools provided by NodeJS. When you enter the check-in/update page of an Event Session, or enter into the Visit page, Hopper broadcasts this information to your Hopper Client on your desktop, "awakening" your client.</p>
		<p><strong>You are not required to use Hopper Client -- the same drag and drop area above will accept a file!</strong></p>
    </div><!-- /.box-body -->
</div><!-- /.box -->