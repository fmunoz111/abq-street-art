import {Injectable} from "@angular/core";
import {HttpClient} from "@angular/common/http";

import {Status} from "../classes/status";
import {Profile} from "../classes/profile";
import {Art} from "../classes/art";
import {Observable} from "rxjs/Observable";

@Injectable ()
export class ProfileService {

	constructor(protected http: HttpClient) {
	}

	//define the API endpoint
	private commentUrl = "api/comment/";


	//call the Comment API and create a new comment
	createComment(comment : Comment) : Observable<Status> {
		return (this.http.post<Status>(this.commentUrl, comment));
	}

	// call to the Comment API and get a Comment object by its id
	getCommentByCommentId(id: number) : Observable<Comment> {
		return(this.http.get<Comment>(this.commentUrl + id));
	}

	// call to the Comment API and get a Comment object by its foreign key, profile id
	getCommentByCommentProfileId(commentProfileId: number) : Observable<Comment[]> {
		return(this.http.get<Comment[]>(this.commentUrl + commentProfileId));
	}

	// call to the Comment API and get a Comment object by its foreign key, art id
	getCommentByCommentArtId(commentArtId: number) : Observable<Comment[]> {
		return(this.http.get<Comment[]>(this.commentUrl + commentArtId));
	}
}